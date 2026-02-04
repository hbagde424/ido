<?php

namespace App\Http\Controllers;

use App\ProjectChecklist;
use App\ProjectTask;
use App\ProjectTaskComment;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProjectChecklistController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('project_checklist.view') && ! auth()->user()->can('project_checklist.create')) {
            abort(403, 'Unauthorized action.');
        }

        // DataTables sometimes does not send X-Requested-With header depending on client config.
        // Also check for DataTables 'draw' parameter to reliably detect server-side requests.
        if (request()->ajax() || request()->has('draw')) {
            $business_id = request()->session()->get('user.business_id');

            $projects = ProjectChecklist::where('business_id', $business_id)
                        ->with(['createdBy', 'tasks', 'projectLead'])
                        ->select(['id', 'project_name', 'created_by', 'start_date', 'end_date', 'project_lead_id', 'created_at'])
                        // Default server-side ordering: latest projects first
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc');

            // Exclude admin-created projects for subadmins
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            if ($user_role_id == 14) {
                $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
                $projects->whereNotIn('created_by', $admin_user_ids);
            }

            return Datatables::of($projects)
                ->addColumn(
                    'action',
                    '@can("project_checklist.update")
                    <button data-href="{{action(\'App\Http\Controllers\ProjectChecklistController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_project_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("project_checklist.delete")
                        <button data-href="{{action(\'App\Http\Controllers\ProjectChecklistController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_project_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan
                    <a href="{{action(\'App\Http\Controllers\ProjectChecklistController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-eye-open"></i> @lang("messages.view")</a>'
                )
                ->editColumn('created_by', function ($row) {
                    return $row->createdBy ? $row->createdBy->user_full_name : '';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') : '-';
                })
                ->addColumn('end_date', function ($row) {
                    return $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d/m/Y') : '-';
                })
                ->addColumn('project_lead_name', function ($row) {
                    if ($row->projectLead) {
                        return $row->projectLead->user_full_name ?? 
                               trim(($row->projectLead->surname ?? '') . ' ' . 
                                    ($row->projectLead->first_name ?? '') . ' ' . 
                                    ($row->projectLead->last_name ?? ''));
                    }
                    return '-';
                })
                ->addColumn('project_progress', function ($row) {
                    // Calculate progress based on task completion
                    $total_tasks = $row->tasks->count();
                    if ($total_tasks == 0) {
                        $progress = 0;
                    } else {
                        $completed_tasks = $row->tasks->where('status', 1)->count();
                        $progress = round(($completed_tasks / $total_tasks) * 100);
                    }
                    
                    // Determine color based on progress
                    $color = '#dc3545'; // Red (0%)
                    if ($progress > 70) {
                        $color = '#28a745'; // Green (above 70%)
                    } elseif ($progress >= 1) {
                        $color = '#fd7e14'; // Orange (1% to 70%)
                    }
                    
                    return '<div class="progress" style="height: 25px; margin-bottom: 0;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: ' . $progress . '%; background-color: ' . $color . '; color: black; line-height: 25px; font-weight: bold;" 
                                     aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">
                                    ' . $progress . '%
                                </div>
                            </div>';
                })
                ->addColumn('project_status', function ($row) {
                    $statusData = $row->getProjectStatus();
                    $html = '<span class="label label-' . $statusData['old_badge_color'] . '">' . $statusData['old_status'] . '</span>';
                    if ($statusData['extra_status']) {
                        $html .= ' <span class="label label-' . $statusData['extra_badge_color'] . '">' . $statusData['extra_status'] . '</span>';
                    }
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'project_progress', 'project_status'])
                ->orderColumn('start_date', 'start_date $1')
                ->orderColumn('end_date', 'end_date $1')
                ->make(true);
        }

    $business_id = request()->session()->get('user.business_id');

    $projectsQuery = ProjectChecklist::where('business_id', $business_id)
        ->with(['createdBy', 'tasks', 'projectLead'])
        // Default ordering for non-AJAX (client-side) table: latest projects first
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc');

    // Exclude admin-created projects for subadmins
    $currentUser = auth()->user();
    $user_role_id = $currentUser->roleId ?? 0;
    if ($user_role_id == 14) {
        $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
        $projectsQuery->whereNotIn('created_by', $admin_user_ids);
    }

    // If user does not have explicit permission to view all projects, limit to projects assigned to them
    if (! auth()->user()->can('project_checklist.view_all')) {
        $user_id = request()->session()->get('user.id');
        $projectsQuery->whereHas('users', function ($q) use ($user_id) {
            $q->where('users.id', $user_id);
        });
    }

    $projects = $projectsQuery->get();

    // Calculate statistics
    $total_projects = $projects->count();
    $complete_projects = 0;
    $in_progress_projects = 0;
    $incomplete_projects = 0;
    $not_started_projects = 0;
    $overdue_projects = 0;
    $on_hold_projects = 0;
    $cancelled_projects = 0;
    
    foreach ($projects as $project) {
        $statusData = $project->getProjectStatus();
        
        // Count old status (always present)
        switch ($statusData['old_status']) {
            case 'Complete':
                $complete_projects++;
                break;
            case 'In Progress':
                $in_progress_projects++;
                break;
            default:
                $incomplete_projects++;
                break;
        }
        
        // Count extra status if applicable
        if ($statusData['extra_status']) {
            switch ($statusData['extra_status']) {
                case 'Not Started Yet':
                    $not_started_projects++;
                    break;
                case 'Overdue':
                    $overdue_projects++;
                    break;
                case 'On Hold':
                    $on_hold_projects++;
                    break;
                case 'Cancelled':
                    $cancelled_projects++;
                    break;
            }
        }
    }

    return view('project_checklist.index', compact('projects', 'total_projects', 'complete_projects', 'in_progress_projects', 'incomplete_projects', 'not_started_projects', 'overdue_projects', 'on_hold_projects', 'cancelled_projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('project_checklist.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('project_checklist.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('project_checklist.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['project_name', 'start_date', 'end_date', 'project_lead_id']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            
            // Format dates if provided
            if (!empty($input['start_date'])) {
                $input['start_date'] = \Carbon\Carbon::parse($input['start_date'])->format('Y-m-d');
            }
            if (!empty($input['end_date'])) {
                $input['end_date'] = \Carbon\Carbon::parse($input['end_date'])->format('Y-m-d');
            }

            $project = ProjectChecklist::create($input);
            // sync assigned users if provided
            if ($request->has('assigned_users')) {
                $project->users()->sync(array_filter((array) $request->input('assigned_users')));
            }
            
            $output = ['success' => true,
                'data' => $project,
                'msg' => __('Project created successfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return $output;
        } else {
            if ($output['success']) {
                return redirect()->action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], $project->id)
                    ->with('status', $output['msg']);
            } else {
                return redirect()->back()->with('error', $output['msg']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! auth()->user()->can('project_checklist.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Handle AJAX request for tasks with server-side processing
        if (request()->ajax() || request()->has('draw')) {
            $projectQuery = ProjectChecklist::where('business_id', $business_id)
                ->where('id', $id);

            // If user does not have permission to view all projects, ensure the project is assigned to them
            if (! auth()->user()->can('project_checklist.view_all')) {
                $user_id = request()->session()->get('user.id');
                $projectQuery->whereHas('users', function ($q) use ($user_id) {
                    $q->where('users.id', $user_id);
                });
            }

            $project = $projectQuery->with('users')->firstOrFail();
            
            // Check if current user is admin or project lead
            $current_user_id = request()->session()->get('user.id');
            $current_user = auth()->user();
            $is_admin = $current_user->hasRole('Admin#'.$business_id);
            $is_project_lead = $project->project_lead_id == $current_user_id;
            $can_manage_status = $is_admin || $is_project_lead;

            $tasks = ProjectTask::where('project_checklist_id', $id)
                ->with(['assignedUser', 'assignedUser.department'])
                ->select(['id', 'task_name', 'status', 'remark', 'start_date', 'end_date', 'user_id', 'created_at'])
                ->orderBy('created_at', 'desc');

            return Datatables::of($tasks)
                ->addColumn('sr_no', function ($row) {
                    static $counter = 0;
                    return ++$counter;
                })
                ->editColumn('status', function ($row) use ($can_manage_status) {
                    $checked = $row->status ? 'checked' : '';
                    $disabled = !$can_manage_status ? 'disabled' : '';
                    $title = !$can_manage_status ? 'title="Only admin and project lead can change task status"' : '';
                    return '<input type="checkbox" class="task-status" data-task-id="' . $row->id . '" ' . $checked . ' ' . $disabled . ' ' . $title . '>';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') : '-';
                })
                ->addColumn('end_date', function ($row) {
                    return $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d/m/Y') : '-';
                })
                ->addColumn('user_name', function ($row) {
                    if ($row->assignedUser) {
                        $user = $row->assignedUser;
                        return $user->user_full_name ?? trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    }
                    return '-';
                })
                ->addColumn('department', function ($row) {
                    if ($row->assignedUser && $row->assignedUser->department) {
                        return $row->assignedUser->department->name ?? '-';
                    }
                    return '-';
                })
                ->addColumn('task_update', function ($row) {
                    // Check if task is complete (status = 1) or incomplete (status = 0)
                    $is_complete = $row->status == 1;
                    $color = $is_complete ? 'green' : 'red';
                    $text = $is_complete ? 'Complete' : 'Incomplete';
                    $bg_color = $is_complete ? '#28a745' : '#dc3545';
                    
                    return '<div class="task-status-bar" style="background-color: ' . $bg_color . '; color: white; padding: 8px 12px; text-align: center; border-radius: 4px; font-weight: bold; min-width: 120px;">
                                ' . $text . '
                            </div>';
                })
                ->editColumn('remark', function ($row) {
                    return '<textarea class="form-control task-remark" data-task-id="' . $row->id . '" rows="2">' . htmlspecialchars(strip_tags($row->remark)) . '</textarea>';
                })
                ->addColumn('timeline', function ($row) {
                    // Count comments for this task
                    $comment_count = 0;
                    try {
                        if (Schema::hasTable('project_task_comments')) {
                            $comment_count = \App\ProjectTaskComment::where('project_task_id', $row->id)->count();
                        }
                    } catch (\Exception $e) {
                        // Table doesn't exist or error
                    }
                    return '<button class="btn btn-xs btn-success view-timeline-btn" data-task-id="' . $row->id . '" data-task-name="' . htmlspecialchars($row->task_name) . '">
                                <i class="fa fa-clock-o"></i> ' . __('View') . ' (' . $comment_count . ')
                            </button>';
                })
                ->addColumn('action', function ($row) use ($is_admin, $is_project_lead) {
                    $buttons = '<button class="btn btn-xs btn-info add-comment-btn" data-task-id="' . $row->id . '" data-task-name="' . htmlspecialchars($row->task_name) . '">
                                <i class="fa fa-comment"></i> ' . __('Comment') . '
                            </button>';
                    
                    // Only show edit button for admin or project lead
                    if ($is_admin || $is_project_lead) {
                        $buttons .= ' <button class="btn btn-xs btn-primary edit-task-btn" 
                            data-task-id="' . $row->id . '"
                            data-task-name="' . htmlspecialchars($row->task_name) . '"
                            data-remark="' . htmlspecialchars(strip_tags($row->remark)) . '"
                            data-status="' . ($row->status ? 1 : 0) . '"
                            data-start-date="' . ($row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d/m/Y') : '') . '"
                            data-end-date="' . ($row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d/m/Y') : '') . '"
                            data-user-id="' . ($row->user_id ?? '') . '">
                            <i class="fa fa-edit"></i> ' . __('Edit') . '
                        </button>';
                    }
                    
                    // Only show delete button for admin
                    if ($is_admin) {
                        $buttons .= ' <button class="btn btn-xs btn-danger delete-task" data-task-id="' . $row->id . '">
                                <i class="fa fa-trash"></i> ' . __('Delete') . '
                            </button>';
                    }
                    
                    return $buttons;
                })
                ->rawColumns(['status', 'remark', 'task_update', 'timeline', 'action'])
                ->orderColumn('start_date', 'start_date $1')
                ->orderColumn('end_date', 'end_date $1')
                ->make(true);
        }

        $projectQuery = ProjectChecklist::where('business_id', $business_id)
            ->with(['createdBy', 'updatedBy'])
            ->where('id', $id);

        // If user does not have permission to view all projects, ensure the project is assigned to them
        if (! auth()->user()->can('project_checklist.view_all')) {
            $user_id = request()->session()->get('user.id');
            $projectQuery->whereHas('users', function ($q) use ($user_id) {
                $q->where('users.id', $user_id);
            });
        }

        $project = $projectQuery->with('users')->firstOrFail();
        
        // Update last viewed timestamp
        $project->last_viewed_at = now();
        $project->save();

        // Calculate task statistics
        $all_tasks = ProjectTask::where('project_checklist_id', $id)->get();
        $total_tasks = $all_tasks->count();
        $complete_tasks = $all_tasks->where('status', 1)->count();
        $incomplete_tasks = $total_tasks - $complete_tasks;
        
        // If AJAX request for stats only, return JSON
        if (request()->ajax() && request()->has('get_stats_only')) {
            return response()->json([
                'total_tasks' => $total_tasks,
                'complete_tasks' => $complete_tasks,
                'incomplete_tasks' => $incomplete_tasks
            ]);
        }

        // Get users for task assignment dropdown
        $business_id = request()->session()->get('user.business_id');
        
        // Get only users assigned to this project
        $project_users = $project->users()
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.surname')
            ->get()
            ->mapWithKeys(function ($user) {
                $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                return [$user->id => $full_name];
            })
            ->filter(function ($name) {
                return !empty(trim($name));
            });
        
        // If no users assigned to project, show all business users as fallback
        if ($project_users->isEmpty()) {
            $project_users = \App\User::where('business_id', $business_id)
                ->user()
                ->select('id', 'first_name', 'last_name', 'surname')
                ->get()
                ->mapWithKeys(function ($user) {
                    $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    return [$user->id => $full_name];
                })
                ->filter(function ($name) {
                    return !empty(trim($name));
                });
        }
        
        $users = $project_users;
        
        // Get all business users for assigned users dropdown
        $all_business_users = \App\User::forDropdown($business_id, false);

        return view('project_checklist.show', compact('project', 'users', 'all_business_users', 'total_tasks', 'complete_tasks', 'incomplete_tasks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $project = ProjectChecklist::where('business_id', $business_id)->find($id);

        // If request is AJAX, return modal partial, otherwise return full page
        if (request()->ajax()) {
            return view('project_checklist.edit', compact('project'));
        }

        return view('project_checklist.edit_full', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['project_name', 'start_date', 'end_date', 'project_lead_id']);
                $business_id = $request->session()->get('user.business_id');

                $project = ProjectChecklist::where('business_id', $business_id)->findOrFail($id);
                $project->project_name = $input['project_name'];
                
                // Format dates if provided
                if (!empty($input['start_date'])) {
                    $project->start_date = \Carbon\Carbon::parse($input['start_date'])->format('Y-m-d');
                } else {
                    $project->start_date = null;
                }
                if (!empty($input['end_date'])) {
                    $project->end_date = \Carbon\Carbon::parse($input['end_date'])->format('Y-m-d');
                } else {
                    $project->end_date = null;
                }
                
                $project->project_lead_id = $input['project_lead_id'] ?? null;
                $project->updated_by = $request->session()->get('user.id');
                $project->save();

                if ($request->has('assigned_users')) {
                    $project->users()->sync(array_filter((array) $request->input('assigned_users')));
                }

                $output = ['success' => true,
                    'msg' => __('Project updated successfully'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        } else {
            // For non-AJAX, redirect to show page
            try {
                $input = $request->only(['project_name', 'start_date', 'end_date', 'project_lead_id']);
                $business_id = $request->session()->get('user.business_id');

                $project = ProjectChecklist::where('business_id', $business_id)->findOrFail($id);
                $project->project_name = $input['project_name'];
                
                // Format dates if provided
                if (!empty($input['start_date'])) {
                    $project->start_date = \Carbon\Carbon::parse($input['start_date'])->format('Y-m-d');
                } else {
                    $project->start_date = null;
                }
                if (!empty($input['end_date'])) {
                    $project->end_date = \Carbon\Carbon::parse($input['end_date'])->format('Y-m-d');
                } else {
                    $project->end_date = null;
                }
                
                $project->project_lead_id = $input['project_lead_id'] ?? null;
                $project->updated_by = $request->session()->get('user.id');
                $project->save();

                return redirect()->action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], $id)
                    ->with('status', __('Project updated successfully'));
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                return redirect()->back()->with('error', __('messages.something_went_wrong'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('project_checklist.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $project = ProjectChecklist::where('business_id', $business_id)->findOrFail($id);
                $project->delete();

                $output = ['success' => true,
                    'msg' => __('Project deleted successfully'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Add a task to the project.
     */
    public function addTask(Request $request, $project_id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['task_name', 'status', 'remark', 'start_date', 'end_date', 'user_id']);
            $input['project_checklist_id'] = $project_id;
            // Respect explicit status value sent by client (1 or 0). If not present default to 0.
            $input['status'] = $request->input('status') ? 1 : 0;
            
            // Set default user_id to logged-in user if not provided
            if (empty($input['user_id'])) {
                $input['user_id'] = auth()->user()->id;
            }
            
            // Format dates if provided
            if (!empty($input['start_date'])) {
                $input['start_date'] = \Carbon\Carbon::parse($input['start_date'])->format('Y-m-d');
            }
            if (!empty($input['end_date'])) {
                $input['end_date'] = \Carbon\Carbon::parse($input['end_date'])->format('Y-m-d');
            }

            ProjectTask::create($input);

            $output = ['success' => true,
                'msg' => __('Task added successfully'),
                // After adding a task, stay on the project show page
                'redirect' => action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$project_id]),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        // If request is AJAX return JSON, otherwise redirect back to the project show page
        if (request()->ajax()) {
            return $output;
        } else {
            if ($output['success']) {
                return redirect()->action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$project_id])
                    ->with('status', $output['msg']);
            }

            return redirect()->back()->with('error', $output['msg']);
        }
    }

    /**
     * Update a task.
     */
    public function updateTask(Request $request, $task_id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $task = ProjectTask::with('project')->findOrFail($task_id);
            $project = $task->project;
            $current_user_id = auth()->user()->id;
            $business_id = $project->business_id;
            
            // Check if user is admin or project lead
            $is_admin = auth()->user()->hasRole('Admin#'.$business_id);
            $is_project_lead = $project && $project->project_lead_id == $current_user_id;
            $can_manage_task = $is_admin || $is_project_lead;
            
            // Check if trying to change status without permission
            $new_status = $request->input('status') ? 1 : 0;
            if ($task->status != $new_status && !$can_manage_task) {
                return response()->json([
                    'success' => false,
                    'msg' => __('Only admin and project lead can change task status.')
                ], 403);
            }
            
            // Build update data but do not overwrite task_name with empty string
            $input = [];
            if ($request->has('task_name') && $request->filled('task_name')) {
                $input['task_name'] = $request->input('task_name');
            }
            // Respect explicit status value sent by client (1 or 0). If not present default to 0.
            $input['status'] = $new_status;
            // remark can be empty string intentionally
            if ($request->has('remark')) {
                $input['remark'] = $request->input('remark');
            }
            // Handle start_date and end_date
            if ($request->has('start_date') && $request->filled('start_date')) {
                $input['start_date'] = \Carbon\Carbon::parse($request->input('start_date'))->format('Y-m-d');
            } elseif ($request->has('start_date')) {
                $input['start_date'] = null;
            }
            if ($request->has('end_date') && $request->filled('end_date')) {
                $input['end_date'] = \Carbon\Carbon::parse($request->input('end_date'))->format('Y-m-d');
            } elseif ($request->has('end_date')) {
                $input['end_date'] = null;
            }
            // Handle user_id
            if ($request->has('user_id')) {
                $input['user_id'] = $request->input('user_id');
            }

            $task = ProjectTask::findOrFail($task_id);
            $task->update($input);

            $output = ['success' => true,
                'msg' => __('Task updated successfully'),
                // Keep user on project show page after task update
                'redirect' => action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$task->project_checklist_id]),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

            if (request()->ajax()) {
                return $output;
            } else {
                if ($output['success']) {
                    return redirect()->action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$task->project_checklist_id])
                        ->with('status', $output['msg']);
                }

                return redirect()->back()->with('error', $output['msg']);
            }
    }

    /**
     * Delete a task.
     */
    public function deleteTask($task_id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $task = ProjectTask::findOrFail($task_id);
            $task->delete();

            $output = ['success' => true,
                'msg' => __('Task deleted successfully'),
                // Keep user on project show page after deleting task
                'redirect' => action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$task->project_checklist_id]),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return $output;
        } else {
            if ($output['success']) {
                return redirect()->action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$task->project_checklist_id])
                    ->with('status', $output['msg']);
            }

            return redirect()->back()->with('error', $output['msg']);
        }
    }

    /**
     * Display a listing of all tasks across projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function tasksIndex()
    {
        if (! auth()->user()->can('project_checklist.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $tasksQuery = ProjectTask::with(['project'])
            ->whereHas('project', function ($q) use ($business_id) {
                $q->where('business_id', $business_id);
            })
            ->select(['id', 'project_checklist_id', 'task_name', 'status', 'remark', 'created_at']);

        // Exclude admin-created projects for subadmins
        $currentUser = auth()->user();
        $user_role_id = $currentUser->roleId ?? 0;
        if ($user_role_id == 14) {
            $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
            $tasksQuery->whereHas('project', function ($q) use ($admin_user_ids) {
                $q->whereNotIn('created_by', $admin_user_ids);
            });
        }

        // If user does not have permission to view all projects, limit tasks to projects assigned to them
        if (! auth()->user()->can('project_checklist.view_all')) {
            $user_id = request()->session()->get('user.id');
            $tasksQuery->whereHas('project', function ($q) use ($user_id) {
                $q->whereHas('users', function ($q2) use ($user_id) {
                    $q2->where('users.id', $user_id);
                });
            });
        }

        $tasks = $tasksQuery;

        // Also pass a list of projects for client-side filtering (respecting assignment rules)
        $projectsQuery = ProjectChecklist::where('business_id', $business_id)
            ->select(['id', 'project_name'])
            ->orderBy('project_name');

        // Exclude admin-created projects for subadmins
        if ($user_role_id == 14) {
            $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
            $projectsQuery->whereNotIn('created_by', $admin_user_ids);
        }

        if (! auth()->user()->can('project_checklist.view_all')) {
            $user_id = request()->session()->get('user.id');
            $projectsQuery->whereHas('users', function ($q) use ($user_id) {
                $q->where('users.id', $user_id);
            });
        }

        $projects = $projectsQuery->get();

        return view('project_checklist.tasks_index', compact('tasks', 'projects'));
    }

    /**
     * Store a comment for a task.
     */
    public function storeComment(Request $request, $task_id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'comment' => 'required|string',
                'document' => 'nullable|file|max:10240', // Max 10MB
            ]);

            $task = ProjectTask::findOrFail($task_id);
            
            // Verify task belongs to user's business
            $business_id = request()->session()->get('user.business_id');
            if ($task->project->business_id != $business_id) {
                abort(403, 'Unauthorized action.');
            }

            $data = [
                'project_task_id' => $task_id,
                'user_id' => auth()->user()->id,
                'comment' => $request->input('comment'),
            ];

            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $document_name = $file->getClientOriginalName();
                $document_path = $file->store('project_task_documents', 'public');
                
                $data['document_name'] = $document_name;
                $data['document_path'] = $document_path;
            }

            ProjectTaskComment::create($data);

            // Sync comment to Todo if linked
            if ($task->todo) {
                \Modules\Essentials\Entities\EssentialsTodoComment::create([
                    'task_id' => $task->todo->id,
                    'comment' => $request->input('comment'),
                    'comment_by' => auth()->user()->id,
                ]);
            }

            $output = ['success' => true,
                'msg' => __('Comment added successfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return $output;
        } else {
            return redirect()->back()->with('status', $output['msg']);
        }
    }

    /**
     * Get comments for a task.
     */
    public function getComments($task_id)
    {
        if (! auth()->user()->can('project_checklist.view')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('Unauthorized action.')], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        try {
            $task = ProjectTask::with(['project'])->findOrFail($task_id);
            
            // Verify task belongs to user's business
            $business_id = request()->session()->get('user.business_id');
            if (!$task->project || $task->project->business_id != $business_id) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'msg' => __('Task not found or unauthorized.')], 403);
                }
                abort(403, 'Unauthorized action.');
            }

            // Check if comments table exists, if not return empty collection
            $comments = collect([]);
            try {
                if (Schema::hasTable('project_task_comments')) {
                    $comments = $task->comments()->with('user')->orderBy('created_at', 'desc')->get();
                } else {
                    \Log::warning('project_task_comments table does not exist. Please run migration.');
                }
            } catch (\Exception $tableException) {
                \Log::warning('Comments table may not exist: ' . $tableException->getMessage());
                // Continue with empty collection
            }

            return view('project_checklist.comments', compact('comments', 'task'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Task not found: ' . $task_id);
            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('Task not found.')], 404);
            }
            abort(404, 'Task not found');
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()], 500);
            }
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }

    /**
     * Get timeline view for a task.
     */
    public function getTimeline($task_id)
    {
        if (! auth()->user()->can('project_checklist.view')) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('Unauthorized action.')], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        try {
            $task = ProjectTask::with(['project'])->findOrFail($task_id);
            
            // Verify task belongs to user's business
            $business_id = request()->session()->get('user.business_id');
            if (!$task->project || $task->project->business_id != $business_id) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'msg' => __('Task not found or unauthorized.')], 403);
                }
                abort(403, 'Unauthorized action.');
            }

            // Check if comments table exists, if not return empty collection
            $comments = collect([]);
            try {
                if (Schema::hasTable('project_task_comments')) {
                    $comments = $task->comments()->with('user')->orderBy('created_at', 'desc')->get();
                } else {
                    \Log::warning('project_task_comments table does not exist. Please run migration.');
                }
            } catch (\Exception $tableException) {
                \Log::warning('Comments table may not exist: ' . $tableException->getMessage());
                // Continue with empty collection
            }

            return view('project_checklist.timeline', compact('comments', 'task'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Task not found: ' . $task_id);
            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('Task not found.')], 404);
            }
            abort(404, 'Task not found');
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            if (request()->ajax()) {
                return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()], 500);
            }
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')], 500);
        }
    }

    /**
     * Download task comment document.
     */
    public function downloadDocument($comment_id)
    {
        if (! auth()->user()->can('project_checklist.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $comment = ProjectTaskComment::findOrFail($comment_id);
            $task = $comment->task;
            
            // Verify task belongs to user's business
            $business_id = request()->session()->get('user.business_id');
            if ($task->project->business_id != $business_id) {
                abort(403, 'Unauthorized action.');
            }

            if ($comment->document_path && Storage::disk('public')->exists($comment->document_path)) {
                return Storage::disk('public')->download($comment->document_path, $comment->document_name);
            } else {
                abort(404, 'Document not found');
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            abort(404, 'Document not found');
        }
    }

    /**
     * Update assigned users for a project.
     */
    public function updateAssignedUsers(Request $request, $project_id)
    {
        if (! auth()->user()->can('project_checklist.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $project = ProjectChecklist::where('business_id', $business_id)->findOrFail($project_id);

            // Get assigned users from request
            $assigned_users = $request->input('assigned_users', []);
            if (!is_array($assigned_users)) {
                $assigned_users = $assigned_users ? [$assigned_users] : [];
            }
            $assigned_users = array_filter($assigned_users);

            // Sync assigned users
            $project->users()->sync($assigned_users);

            // Get user names for response
            $user_names = [];
            if (!empty($assigned_users)) {
                $users = \App\User::whereIn('id', $assigned_users)
                    ->select('id', 'first_name', 'last_name', 'surname')
                    ->get();
                foreach ($users as $user) {
                    $full_name = $user->user_full_name ?? trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    if (!empty(trim($full_name))) {
                        $user_names[] = $full_name;
                    }
                }
            }

            $output = [
                'success' => true,
                'msg' => __('Assigned users updated successfully'),
                'user_names' => $user_names
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        if (request()->ajax()) {
            return $output;
        } else {
            if ($output['success']) {
                return redirect()->back()->with('status', $output['msg']);
            } else {
                return redirect()->back()->with('error', $output['msg']);
            }
        }
    }
}
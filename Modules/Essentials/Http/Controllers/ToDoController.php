<?php

namespace Modules\Essentials\Http\Controllers;

use App\Media;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Essentials\Entities\EssentialsTodoComment;
use Modules\Essentials\Entities\ToDo;
use Modules\Essentials\Notifications\NewTaskCommentNotification;
use Modules\Essentials\Notifications\NewTaskDocumentNotification;
use Modules\Essentials\Notifications\NewTaskNotification;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\GlobalFunction;


class ToDoController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;

        $this->priority_colors = [
            'low' => 'bg-green',
            'medium' => 'bg-yellow',
            'high' => 'bg-orange',
            'urgent' => 'bg-red',
        ];

        $this->status_colors = [
            'new' => 'bg-yellow',
            'in_progress' => 'bg-light-blue',
            'on_hold' => 'bg-red',
            'completed' => 'bg-green',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        $auth_id = auth()->user()->id;

        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        if (request()->ajax()) {
            $todos = ToDo::where('business_id', $business_id)
                        ->with(['users.department', 'assigned_by'])
                        ->select('*');

            if (! empty($request->priority)) {
                $todos->where('priority', $request->priority);
            }

            if (! empty($request->status)) {
                $todos->where('status', $request->status);
            }

            // Hardcoded role-based filtering
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId; // Assuming roleId field exists
            
            if ($user_role_id == 1) {
                // Admin role (ID: 1) - See all TODOs (no filtering)
                // No additional filtering applied
            } elseif ($user_role_id == 14) {
                // Sub-admin role (ID: 14) - See TODOs based on location permissions
                // Exclude admin users' data
                $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
                
                $user_permissions = $currentUser->permissions->pluck('name')->all();
                $permitted_locations = [];
                
                // Check for location permissions (handle both formats: location.X and location.location.X)
                foreach ($user_permissions as $permission) {
                    if (strpos($permission, 'location.') === 0) {
                        // Extract location ID from permission
                        if (strpos($permission, 'location.location.') === 0) {
                            // Handle double prefix: location.location.X
                            $location_id = str_replace('location.location.', '', $permission);
                        } else {
                            // Handle single prefix: location.X
                            $location_id = str_replace('location.', '', $permission);
                        }
                        if (is_numeric($location_id)) {
                            $permitted_locations[] = (int)$location_id;
                        }
                    }
                }
                
                if (!empty($permitted_locations)) {
                    // Filter TODOs to only show those assigned to users from permitted locations
                    // Exclude admin users
                    $todos->whereHas('users', function ($q) use ($permitted_locations, $admin_user_ids) {
                        $q->whereIn('location_id', $permitted_locations)
                          ->whereNotIn('user_id', $admin_user_ids);
                    });
                } else {
                    // If no location permissions, show only their own TODOs
                    $todos->whereHas('users', function ($q) use ($auth_id) {
                        $q->where('user_id', $auth_id);
                    });
                }
            } else {
                // Any other role - Show only their own TODOs
                $todos->whereHas('users', function ($q) use ($auth_id) {
                    $q->where('user_id', $auth_id);
                });
            }

            //Filter by user id.
            if (! empty($request->user_id)) {
                $user_id = $request->user_id;
                $todos->whereHas('users', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }

            //Filter by date.
            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
                $todos->whereDate('date', '>=', $start)
                            ->whereDate('date', '<=', $end);
            }

            return Datatables::of($todos)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'.__('messages.actions').'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        if (auth()->user()->can('essentials.edit_todos')) {
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'edit'], [$row->id]).'" class="btn-modal" data-container="#task_modal"><i class="glyphicon glyphicon-edit"></i> '.__('messages.edit').'</a></li>';
                        }

                        if (auth()->user()->can('essentials.delete_todos')) {
                            $html .= '<li><a href="#" data-href="'.action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'destroy'], [$row->id]).'" class="delete_task" ><i class="fa fa-trash"></i> '.__('messages.delete').'</a></li>';
                        }

                        $html .= '<li><a href="'.action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'show'], [$row->id]).'" ><i class="fa fa-eye"></i> '.__('messages.view').'</a></li>';

                        $html .= '<li><a href="#" class="change_status" data-status="'.$row->status.'" data-task_id="'.$row->id.'"><i class="fas fa-check-circle"></i> '.__('essentials::lang.change_status').'</a></li>';

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                // ->editColumn('task1', function ($row) use ($priorities) {
                //     $html = '<a href="'.action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'show'], [$row->id]).'" >'.$row->task.'</a> <br>
                //         <a data-href="'.action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'viewSharedDocs'], [$row->id]).'" class="btn btn-primary btn-xs view-shared-docs">'.__('essentials::lang.docs').'</a>';

                //     if (! empty($row->priority)) {
                //         $bg_color = ! empty($this->priority_colors[$row->priority]) ? $this->priority_colors[$row->priority] : 'bg-gray';

                //         $html .= ' &nbsp; <span class="label '.$bg_color.'"> '.$priorities[$row->priority].'</span>';
                //     }

                //     return $html;
                // })
                ->addColumn('assigned_by', function ($row) {
                    return $row->assigned_by->user_full_name;
                })
                ->addColumn('view_url', function ($row) {
                    return action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'show'], [$row->id]);
                })
                 ->addColumn('department', function ($row) {
                    $users = [];
                    foreach ($row->users as $user) {
                        $users[] =$user->department->name;  //ashok
                    }

                    return implode(', ', $users);
                })
                ->editColumn('users', function ($row) {
                    $users = [];
                    foreach ($row->users as $user) {
                        $users[] = $user->user_full_name ;  //ashok
                    }

                    return implode(', ', $users);
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('date', '{{@format_datetime($date)}}')
                ->editColumn('end_date', '@if(!empty($end_date)) {{@format_datetime($end_date)}} @endif')
                ->addColumn('estimated_time', function ($row) {
                    if (!empty($row->date)) {
                        $start = \Carbon\Carbon::parse($row->date);
                        $now = \Carbon\Carbon::now();
                        
                        // For Completed tasks, calculate from start date to end date
                        if ($row->status === 'Completed' && !empty($row->end_date)) {
                            $end = \Carbon\Carbon::parse($row->end_date);
                            
                            // Check if end date is valid (not a placeholder like 30-11--0001)
                            if ($end->year > 1900 && $end->year < 2100) {
                                $diff = $start->diff($end);
                                $time_parts = [];
                                
                                if ($diff->days > 0) {
                                    $time_parts[] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
                                }
                                if ($diff->h > 0) {
                                    $time_parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                                }
                                if ($diff->i > 0) {
                                    $time_parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                                }
                                
                                if (empty($time_parts)) {
                                    return '<span class="estimated-time-label text-muted" title="Task completed instantly">Instant</span>';
                                }
                                
                                $tooltip = 'From: ' . $start->format('M d, Y H:i') . ' To: ' . $end->format('M d, Y H:i');
                                return '<span class="estimated-time-label label-success" title="' . $tooltip . '">' . implode(', ', $time_parts) . '</span>';
                            } else {
                                // Invalid end date, calculate from start to current time
                                $diff = $start->diff($now);
                                $time_parts = [];
                                
                                if ($diff->days > 0) {
                                    $time_parts[] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
                                }
                                if ($diff->h > 0) {
                                    $time_parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                                }
                                if ($diff->i > 0) {
                                    $time_parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                                }
                                
                                $tooltip = 'From: ' . $start->format('M d, Y H:i') . ' To: ' . $now->format('M d, Y H:i') . ' (Current Time)';
                                return '<span class="estimated-time-label label-success" title="' . $tooltip . '">' . implode(', ', $time_parts) . '</span>';
                            }
                        }
                        // For In Progress and Incomplete tasks, calculate from start date to current time
                        else if (in_array($row->status, ['In progress', 'Incomplete'])) {
                            $diff = $start->diff($now);
                            $time_parts = [];
                            
                            if ($diff->days > 0) {
                                $time_parts[] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
                            }
                            if ($diff->h > 0) {
                                $time_parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                            }
                            if ($diff->i > 0) {
                                $time_parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                            }
                            
                            if (empty($time_parts)) {
                                return '<span class="estimated-time-label text-muted" title="Task started just now">Just started</span>';
                            }
                            
                            $tooltip = 'From: ' . $start->format('M d, Y H:i') . ' To: ' . $now->format('M d, Y H:i') . ' (Current Time)';
                            $label_class = $row->status === 'In progress' ? 'label-warning' : 'label-danger';
                            return '<span class="estimated-time-label ' . $label_class . '" title="' . $tooltip . '">' . implode(', ', $time_parts) . '</span>';
                        }
                        // For Completed tasks without valid end date, calculate from start to current time
                        else if ($row->status === 'Completed') {
                            $diff = $start->diff($now);
                            $time_parts = [];
                            
                            if ($diff->days > 0) {
                                $time_parts[] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
                            }
                            if ($diff->h > 0) {
                                $time_parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                            }
                            if ($diff->i > 0) {
                                $time_parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                            }
                            
                            $tooltip = 'From: ' . $start->format('M d, Y H:i') . ' To: ' . $now->format('M d, Y H:i') . ' (Current Time)';
                            return '<span class="estimated-time-label label-success" title="' . $tooltip . '">' . implode(', ', $time_parts) . '</span>';
                        }
                        // Fallback for any other status
                        else {
                            $diff = $start->diff($now);
                            $time_parts = [];
                            
                            if ($diff->days > 0) {
                                $time_parts[] = $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
                            }
                            if ($diff->h > 0) {
                                $time_parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                            }
                            if ($diff->i > 0) {
                                $time_parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                            }
                            
                            $tooltip = 'From: ' . $start->format('M d, Y H:i') . ' To: ' . $now->format('M d, Y H:i') . ' (Current Time)';
                            return '<span class="estimated-time-label label-info" title="' . $tooltip . '">' . implode(', ', $time_parts) . '</span>';
                        }
                    }
                    return '<span class="estimated-time-label text-muted" title="Start date not set">No start date</span>';
                })
                ->editColumn('status', function ($row) use ($task_statuses) {
                    $html = '';
                    if (! empty($task_statuses[$row->status])) {
                        $bg_color = ! empty($this->status_colors[$row->status]) ? $this->status_colors[$row->status] : 'bg-gray';

                        $html = '<a href="#" class="change_status" data-status="'.$row->status.'" data-task_id="'.$row->id.'"><span class="label '.$bg_color.'"> '.$task_statuses[$row->status].'</span></a>';
                    }

                    return $html;
                })
                ->editColumn('description', function ($row) {
    return $row->description; // You can also sanitize if needed
})
                ->removeColumn('id')
               ->rawColumns(['task', 'action', 'status', 'description', 'estimated_time'])
                ->make(true);
        }

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            
            if ($user_role_id == 1) {
                // Admin role - Show all users
                $users = User::forDropdown($business_id, false, false, false, true);
            } elseif ($user_role_id == 14) {
                // Sub-admin role - Show users from permitted locations only
                $permitted_locations = $currentUser->permitted_locations();
                
                if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                    // Get users from permitted locations
                    $location_users = User::where('business_id', $business_id)
                        ->user()
                        ->whereIn('location_id', $permitted_locations)
                        ->select('id', 'first_name', 'last_name', 'surname')
                        ->get();
                    
                    // Format users for dropdown
                    $users = $location_users->mapWithKeys(function ($user) {
                        $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        return [$user->id => $full_name];
                    })->filter(function ($name) {
                        return !empty(trim($name));
                    });
                } else {
                    // If no location permissions, show only themselves
                    $current_user = User::find($auth_id);
                    if ($current_user) {
                        $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                        $users = [$auth_id => $full_name];
                    }
                }
            } else {
                // Other roles - Show only themselves
                $current_user = User::find($auth_id);
                if ($current_user) {
                    $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                    $users = [$auth_id => $full_name];
                }
            }
            
            //echo "<pre>";print_r($users);die;
        }

        // Check if dashboard view is requested
        if (request()->get('view') === 'dashboard') {
            return view('essentials::todo.dashboard')->with(compact('users', 'task_statuses', 'priorities'));
        }

        return view('essentials::todo.index')->with(compact('users', 'task_statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
         $auth_id = auth()->user()->id;
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.add_todos')) {
            abort(403, 'Unauthorized action.');
        }

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            
            if ($user_role_id == 1) {
                // Admin role - Show all users
                $users = User::forDropdown($business_id, false, false, false, true);
            } elseif ($user_role_id == 14) {
                // Sub-admin role - Show users from permitted locations only
                // Exclude admin users
                $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
                
                $permitted_locations = $currentUser->permitted_locations();
                
                if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                    // Get users from permitted locations, excluding admins
                    $location_users = User::where('business_id', $business_id)
                        ->user()
                        ->whereIn('location_id', $permitted_locations)
                        ->whereNotIn('id', $admin_user_ids)
                        ->select('id', 'first_name', 'last_name', 'surname')
                        ->get();
                    
                    // Format users for dropdown
                    $users = $location_users->mapWithKeys(function ($user) {
                        $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        return [$user->id => $full_name];
                    })->filter(function ($name) {
                        return !empty(trim($name));
                    });
                } else {
                    // If no location permissions, show only themselves
                    $current_user = User::find($auth_id);
                    if ($current_user) {
                        $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                        $users = [$auth_id => $full_name];
                    }
                }
            } else {
                // Other roles - Show only themselves
                $current_user = User::find($auth_id);
                if ($current_user) {
                    $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                    $users = [$auth_id => $full_name];
                }
            }
        }
        if (! empty(request()->input('from_calendar'))) {
            $users = [];
        }

        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        // Get projects for the business
        $projects = \App\ProjectChecklist::where('business_id', $business_id)
            ->pluck('project_name', 'id')
            ->toArray();

        return view('essentials::todo.create')->with(compact('users', 'task_statuses', 'priorities', 'projects'));
    }

    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $query = ToDo::where('business_id', $business_id)
                        ->with([
                            'assigned_by',
                            'comments',
                            'comments.added_by',
                            'media',
                            'users',
                            'media.uploaded_by_user',
                        ]);

            // Role-based access control consistent with index method
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            $auth_id = auth()->user()->id;
            
            if ($user_role_id == 1) {
                // Admin role (ID: 1) - See all TODOs (no filtering)
                // No additional filtering applied
            } elseif ($user_role_id == 14) {
                // Sub-admin role (ID: 14) - See TODOs based on location permissions
                // Exclude admin users' data
                $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
                
                $user_permissions = $currentUser->permissions->pluck('name')->all();
                $permitted_locations = [];
                
                // Check for location permissions (handle both formats: location.X and location.location.X)
                foreach ($user_permissions as $permission) {
                    if (strpos($permission, 'location.') === 0) {
                        // Extract location ID from permission
                        if (strpos($permission, 'location.location.') === 0) {
                            // Handle double prefix: location.location.X
                            $location_id = str_replace('location.location.', '', $permission);
                        } else {
                            // Handle single prefix: location.X
                            $location_id = str_replace('location.', '', $permission);
                        }
                        if (is_numeric($location_id)) {
                            $permitted_locations[] = (int)$location_id;
                        }
                    }
                }
                
                if (!empty($permitted_locations)) {
                    // Filter TODOs to only show those assigned to users from permitted locations
                    // Exclude admin users
                    $query->whereHas('users', function ($q) use ($permitted_locations, $admin_user_ids) {
                        $q->whereIn('location_id', $permitted_locations)
                          ->whereNotIn('user_id', $admin_user_ids);
                    });
                } else {
                    // If no location permissions, show only their own TODOs
                    $query->whereHas('users', function ($q) use ($auth_id) {
                        $q->where('user_id', $auth_id);
                    });
                }
            } else {
                // Any other role - Show only their own TODOs
                $query->whereHas('users', function ($q) use ($auth_id) {
                    $q->where('user_id', $auth_id);
                });
            }

            $todo = $query->findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Error loading todo: ' . $e->getMessage());
            abort(404, 'Task not found or you do not have permission to view it.');
        }

        $users = [];
        foreach ($todo->users as $user) {
            $users[] = $user->user_full_name;
        }
        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        $activities = Activity::forSubject($todo)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('essentials::todo.view')->with(compact(
            'todo',
            'users',
            'task_statuses',
            'priorities',
            'activities'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.edit_todos')) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        $query = ToDo::where('business_id', $business_id);

        // Check location permissions for all users
        $currentUser = auth()->user();
        if ($currentUser && !$currentUser->can('access_all_locations')) {
            // Get permitted locations for the current user
            $permitted_locations = $currentUser->permitted_locations();
            
            if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                // Filter TODOs to only show those assigned to users from permitted locations
                $query->whereHas('users', function ($q) use ($permitted_locations) {
                    $q->whereIn('location_id', $permitted_locations);
                });
            } else {
                // If no location permissions, show only assigned tasks
                $query->whereHas('users', function ($q) {
                    $q->where('user_id', auth()->user()->id);
                });
            }
        }

        $todo = $query->with(['users'])->findOrFail($id);

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            $auth_id = auth()->user()->id;
            
            if ($user_role_id == 1) {
                // Admin role - Show all users
                $users = User::forDropdown($business_id, false, false, false, true);
            } elseif ($user_role_id == 14) {
                // Sub-admin role - Show users from permitted locations only
                // Exclude admin users
                $admin_user_ids = $this->commonUtil->getAdminUserIds($business_id);
                
                $permitted_locations = $currentUser->permitted_locations();
                
                if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                    // Get users from permitted locations, excluding admins
                    $location_users = User::where('business_id', $business_id)
                        ->user()
                        ->whereIn('location_id', $permitted_locations)
                        ->whereNotIn('id', $admin_user_ids)
                        ->select('id', 'first_name', 'last_name', 'surname')
                        ->get();
                    
                    // Format users for dropdown
                    $users = $location_users->mapWithKeys(function ($user) {
                        $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        return [$user->id => $full_name];
                    })->filter(function ($name) {
                        return !empty(trim($name));
                    });
                } else {
                    // If no location permissions, show only themselves
                    $current_user = User::find($auth_id);
                    if ($current_user) {
                        $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                        $users = [$auth_id => $full_name];
                    }
                }
            } else {
                // Other roles - Show only themselves
                $current_user = User::find($auth_id);
                if ($current_user) {
                    $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                    $users = [$auth_id => $full_name];
                }
            }
        }
        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        // Get projects for the business
        $projects = \App\ProjectChecklist::where('business_id', $business_id)
            ->pluck('project_name', 'id')
            ->toArray();

        return view('essentials::todo.edit')->with(compact('users', 'todo', 'task_statuses', 'priorities', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        
       // dd($request->all());
        $business_id = $request->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.add_todos')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $created_by = $request->session()->get('user.id');
                $input = $request->only(
                    'task',
                    'date',
                    'description',
                    'estimated_hours',
                    'priority',
                    'status',
                    'end_date',
                    'user_id',
                    'project_checklist_id'
                );

                $input['date'] = $this->commonUtil->uf_date($input['date'], true);
                $input['end_date'] = ! empty($input['end_date']) ? $this->commonUtil->uf_date($input['end_date'], true) : null;
                $input['business_id'] = $business_id;
                $input['created_by'] = $created_by;
                $input['status'] = ! empty($input['status']) ? $input['status'] : 'new';

                $users = $request->input('users');
                //Can add only own tasks if permission not given
                if (! auth()->user()->can('essentials.assign_todos') || empty($users)) {
                    $users = [$created_by];
                }
                  $input['user_id'] =$users[0];
               // dd($input);
            $ref_count = $this->commonUtil->setAndGetReferenceCount('essentials_todos');
                //Generate reference number
                $settings = request()->session()->get('business.essentials_settings');
                $settings = ! empty($settings) ? json_decode($settings, true) : [];
                $prefix = ! empty($settings['essentials_todos_prefix']) ? $settings['essentials_todos_prefix'] : '';
                $input['task_id'] = $this->commonUtil->generateReferenceNumber('essentials_todos', $ref_count, null, $prefix);

                $to_dos = ToDo::create($input);

                $to_dos->users()->sync($users);

                //Exclude created user from notification
                $users = $to_dos->users->filter(function ($item) use ($created_by) {
                    return $item->id != $created_by;
                });

                $this->commonUtil->activityLog($to_dos, 'added');

                \Notification::send($users, new NewTaskNotification($to_dos));

                    $user = User::find( $request->input('users'));
                     if(!empty($user[0]['fcmtoken'])){
                    $firebasedata = GlobalFunction::sendPushNotificationToUser('New Todo Added', $user[0]['fcmtoken'], '0');
                    }

                $output = [
                    'user'=>$user,
                    'firebasenew'=>$firebasedata,
                    'file'=>is_readable($filePath),
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                    'todo_id' => $to_dos->id,
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.edit_todos')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                if (! $request->has('only_status')) {
                    $input = $request->only(
                        'task',
                        'date',
                        'description',
                        'estimated_hours',
                        'priority',
                        'status',
                        'end_date',
                        'project_checklist_id'
                    );

                    $input['date'] = $this->commonUtil->uf_date($input['date'], true);
                    $input['end_date'] = ! empty($input['end_date']) ? $this->commonUtil->uf_date($input['end_date'], true) : null;

                    $input['status'] = ! empty($input['status']) ? $input['status'] : 'new';
                    
                    // Auto-fill end_date when status changes to "Completed" in full update
                    if ($input['status'] === 'Completed' && empty($input['end_date'])) {
                        $input['end_date'] = now();
                    }
                } else {
                    $input = ['status' => ! empty($request->input('status')) ? $request->input('status') : null];
                    
                    // Auto-fill end_date when status changes to "Completed"
                    if ($input['status'] === 'Completed') {
                        $input['end_date'] = now();
                    }
                }

                $query = ToDo::where('business_id', $business_id);

                // Check location permissions for all users
                $currentUser = auth()->user();
                if ($currentUser && !$currentUser->can('access_all_locations')) {
                    // Get permitted locations for the current user
                    $permitted_locations = $currentUser->permitted_locations();
                    
                    if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                        // Filter TODOs to only show those assigned to users from permitted locations
                        $query->whereHas('users', function ($q) use ($permitted_locations) {
                            $q->whereIn('location_id', $permitted_locations);
                        });
                    } else {
                        // If no location permissions, show only assigned tasks
                        $query->whereHas('users', function ($q) {
                            $q->where('user_id', auth()->user()->id);
                        });
                    }
                }

                $todo = $query->findOrFail($id);

                $todo_before = $todo->replicate();

                $todo->update($input);

                if (auth()->user()->can('essentials.assign_todos') && ! $request->has('only_status')) {
                    $users = $request->input('users');
                    $todo->users()->sync($users);
                }

                $this->commonUtil->activityLog($todo, 'edited', $todo_before);

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.delete_todos')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

                $todo = ToDo::where('business_id', $business_id);
                
                // Check location permissions for all users
                $currentUser = auth()->user();
                if ($currentUser && !$currentUser->can('access_all_locations')) {
                    // Get permitted locations for the current user
                    $permitted_locations = $currentUser->permitted_locations();
                    
                    if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                        // Filter TODOs to only show those assigned to users from permitted locations
                        $todo->whereHas('users', function ($q) use ($permitted_locations) {
                            $q->whereIn('location_id', $permitted_locations);
                        });
                    } else {
                        // If no location permissions, can destroy only assigned tasks
                        $todo->whereHas('users', function ($q) {
                            $q->where('user_id', auth()->user()->id);
                        });
                    }
                }
                $todo->where('id', $id)
                    ->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Add comment to the task
     *
     * @param  Request  $request
     * @return Response
     */
    public function addComment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['task_id', 'comment']);
                $query = ToDo::where('business_id', $business_id)
                            ->with('users');
                $auth_id = auth()->user()->id;

                // Check location permissions for all users
                $currentUser = auth()->user();
                if ($currentUser && !$currentUser->can('access_all_locations')) {
                    // Get permitted locations for the current user
                    $permitted_locations = $currentUser->permitted_locations();
                    
                    if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                        // Filter TODOs to only show those assigned to users from permitted locations
                        $query->whereHas('users', function ($q) use ($permitted_locations) {
                            $q->whereIn('location_id', $permitted_locations);
                        });
                    } else {
                        // If no location permissions, can add comment to only assigned tasks
                        $query->whereHas('users', function ($q) {
                            $q->where('user_id', auth()->user()->id);
                        });
                    }
                }

                $todo = $query->findOrFail($input['task_id']);

                $input['comment_by'] = $auth_id;

                $comment = EssentialsTodoComment::create($input);

                    DB::table('essentials_to_dos')
                    ->where('id', $input['task_id'])
                    ->update(['is_read' => 1]);


                $comment_html = view('essentials::todo.comment')
                                ->with(compact('comment'))
                                ->render();
                $output = [
                    'success' => true,
                    'comment_html' => $comment_html,
                    'msg' => __('lang_v1.success1111'),
                ];

                //Remove auth user from users collection
                $users = $todo->users->filter(function ($user) use ($auth_id) {
                    return $user->id != $auth_id;
                });

                \Notification::send($users, new NewTaskCommentNotification($comment));
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Upload documents for a task
     *
     * @param  Request  $request
     * @return Response
     */
    public function uploadDocument(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $task_id = $request->input('task_id');
            $query = ToDo::with('users')->where('business_id', $business_id);
            $auth_id = auth()->user()->id;

            // Check location permissions for all users
            $currentUser = auth()->user();
            if ($currentUser && !$currentUser->can('access_all_locations')) {
                // Get permitted locations for the current user
                $permitted_locations = $currentUser->permitted_locations();
                
                if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                    // Filter TODOs to only show those assigned to users from permitted locations
                    $query->whereHas('users', function ($q) use ($permitted_locations) {
                        $q->whereIn('location_id', $permitted_locations);
                    });
                } else {
                    // If no location permissions, can upload documents to only assigned tasks
                    $query->whereHas('users', function ($q) {
                        $q->where('user_id', auth()->user()->id);
                    });
                }
            }

            $todo = $query->findOrFail($task_id);

            Media::uploadMedia($todo->business_id, $todo, $request, 'documents');

            //Remove auth user from users collection
            $users = $todo->users->filter(function ($user) use ($auth_id) {
                return $user->id != $auth_id;
            });

            $data = [
                'task_id' => $todo->task_id,
                'uploaded_by' => $auth_id,
                'id' => $todo->id,
                'uploaded_by_user_name' => auth()->user()->user_full_name,
            ];

            \Notification::send($users, new NewTaskDocumentNotification($data));

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return back()->with('status', $output);
    }

    /**
     * Delete comment of a task
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteComment($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $comment = EssentialsTodoComment::where('comment_by', auth()->user()->id)
                                    ->where('id', $id)
                                    ->delete();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Delete comment of a task
     *
     * @param  int  $id
     * @return Response
     */
    public function deleteDocument($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $media = Media::findOrFail($id);
            if ($media->model_type == 'Modules\Essentials\Entities\ToDo') {
                $todo = ToDo::findOrFail($media->model_id);

                //Can delete document only if task is assigned by or assigned to the user
                if (in_array(auth()->user()->id, [$todo->user_id, $todo->created_by])) {
                    unlink($media->display_path);
                    $media->delete();
                }
            }
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function viewSharedDocs($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $module_data = $this->moduleUtil->getModuleData('getSharedSpreadsheetForGivenData', ['business_id' => $business_id, 'shared_with' => 'todo', 'shared_id' => $id]);

            $sheets = [];
            if (! empty($module_data['Spreadsheet'])) {
                $sheets = $module_data['Spreadsheet'];
            }

            $todo = ToDo::findOrFail($id);

            return view('essentials::todo.view_shared_docs')
                ->with(compact('sheets', 'todo'));
        }
    }

    /**
     * Get task status chart data for dashboard
     *
     * @return Response
     */
    public function getTaskStatusChartData(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $auth_id = auth()->user()->id;
        $currentUser = auth()->user();
        $user_role_id = $currentUser->roleId;

        // Base query for tasks
        $query = ToDo::where('business_id', $business_id)
            ->with(['users']);

        // Apply filters from request
        if (!empty($request->priority)) {
            $query->where('priority', $request->priority);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->user_id)) {
            $user_id = $request->user_id;
            $query->whereHas('users', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
            $query->whereDate('date', '>=', $start)
                  ->whereDate('date', '<=', $end);
        }

        // Apply role-based filtering (same logic as index method)
        if ($user_role_id == 1) {
            // Admin role - See all TODOs
        } elseif ($user_role_id == 14) {
            // Sub-admin role - Filter by location permissions
            $user_permissions = $currentUser->permissions->pluck('name')->all();
            $permitted_locations = [];
            
            foreach ($user_permissions as $permission) {
                if (strpos($permission, 'location.') === 0) {
                    if (strpos($permission, 'location.location.') === 0) {
                        $location_id = str_replace('location.location.', '', $permission);
                    } else {
                        $location_id = str_replace('location.', '', $permission);
                    }
                    if (is_numeric($location_id)) {
                        $permitted_locations[] = (int)$location_id;
                    }
                }
            }
            
            if (!empty($permitted_locations)) {
                $query->whereHas('users', function ($q) use ($permitted_locations) {
                    $q->whereIn('location_id', $permitted_locations);
                });
            } else {
                $query->whereHas('users', function ($q) use ($auth_id) {
                    $q->where('user_id', $auth_id);
                });
            }
        } else {
            // Other roles - Show only their own TODOs
            $query->whereHas('users', function ($q) use ($auth_id) {
                $q->where('user_id', $auth_id);
            });
        }

        // Get overall status distribution
        $status_distribution = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get status distribution by employee with same filters
        $employee_query = DB::table('essentials_to_dos as td')
            ->join('essentials_todos_users as tu', 'td.id', '=', 'tu.todo_id')
            ->join('users as u', 'tu.user_id', '=', 'u.id')
            ->where('td.business_id', $business_id);

        // Apply same filters to employee query
        if (!empty($request->priority)) {
            $employee_query->where('td.priority', $request->priority);
        }

        if (!empty($request->status)) {
            $employee_query->where('td.status', $request->status);
        }

        if (!empty($request->user_id)) {
            $employee_query->where('tu.user_id', $request->user_id);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $employee_query->whereDate('td.date', '>=', $request->start_date)
                           ->whereDate('td.date', '<=', $request->end_date);
        }

        $employee_status_data = $employee_query->select(
                'u.id as user_id',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as employee_name"),
                'td.status',
                DB::raw('count(*) as count')
            )
            ->groupBy('u.id', 'employee_name', 'td.status')
            ->get();

        // Format data for charts
        $task_statuses = ToDo::getTaskStatus();
        
        // Pie chart data
        $pie_data = [];
        $total_tasks = array_sum($status_distribution);
        foreach ($task_statuses as $status_key => $status_label) {
            $count = $status_distribution[$status_key] ?? 0;
            $percentage = $total_tasks > 0 ? round(($count / $total_tasks) * 100, 1) : 0;
            $pie_data[] = [
                'name' => $status_label,
                'value' => $count,
                'percentage' => $percentage
            ];
        }

        // Bar chart data by employee
        $employees = [];
        $status_colors = [
            'Completed' => '#28a745',
            'In progress' => '#17a2b8', 
            'Incomplete' => '#dc3545'
        ];

        foreach ($employee_status_data as $row) {
            $employee_name = trim($row->employee_name);
            if (!isset($employees[$employee_name])) {
                $employees[$employee_name] = [
                    'name' => $employee_name,
                    'user_id' => $row->user_id,
                    'Completed' => 0,
                    'In progress' => 0, 
                    'Incomplete' => 0,
                    'total' => 0
                ];
            }
            $employees[$employee_name][$row->status] = $row->count;
            $employees[$employee_name]['total'] += $row->count;
        }

        // Sort employees by total tasks descending
        uasort($employees, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // If no data exists, provide sample data structure
        if ($total_tasks == 0) {
            $pie_data = [
                ['name' => 'Completed', 'value' => 0, 'percentage' => 0],
                ['name' => 'In Progress', 'value' => 0, 'percentage' => 0],
                ['name' => 'Incomplete', 'value' => 0, 'percentage' => 0]
            ];
            $employees = [];
        }

        return response()->json([
            'pie_data' => $pie_data,
            'employee_data' => array_values($employees),
            'status_colors' => $status_colors,
            'total_tasks' => $total_tasks
        ]);
    }

    /**
     * Get calendar data for todo tasks
     *
     * @param  Request  $request
     * @return Response
     */
    public function getCalendarData(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $start_date = $request->get('start');
        $end_date = $request->get('end');
        $user_id = $request->get('user_id');

        $events = [];

        // Base query for todos
        $query = ToDo::where('business_id', $business_id)
            ->with(['users', 'assigned_by']);

        // Apply role-based filtering (same logic as index method)
        $currentUser = auth()->user();
        $user_role_id = $currentUser->roleId ?? 0;
        $auth_id = auth()->user()->id;
        
        if ($user_role_id == 1) {
            // Admin role - See all TODOs
        } elseif ($user_role_id == 14) {
            // Sub-admin role - Filter by location permissions
            $user_permissions = $currentUser->permissions->pluck('name')->all();
            $permitted_locations = [];
            
            foreach ($user_permissions as $permission) {
                if (strpos($permission, 'location.') === 0) {
                    if (strpos($permission, 'location.location.') === 0) {
                        $location_id = str_replace('location.location.', '', $permission);
                    } else {
                        $location_id = str_replace('location.', '', $permission);
                    }
                    if (is_numeric($location_id)) {
                        $permitted_locations[] = (int)$location_id;
                    }
                }
            }
            
            if (!empty($permitted_locations)) {
                $query->whereHas('users', function ($q) use ($permitted_locations) {
                    $q->whereIn('location_id', $permitted_locations);
                });
            } else {
                $query->whereHas('users', function ($q) use ($auth_id) {
                    $q->where('user_id', $auth_id);
                });
            }
        } else {
            // Other roles - Show only their own TODOs
            $query->whereHas('users', function ($q) use ($auth_id) {
                $q->where('user_id', $auth_id);
            });
        }

        // Filter by date range
        if ($start_date && $end_date) {
            $query->where(function($q) use ($start_date, $end_date) {
                $q->whereBetween('date', [$start_date, $end_date])
                  ->orWhereBetween('end_date', [$start_date, $end_date]);
            });
        }

        // Filter by user if specified
        if ($user_id) {
            $query->whereHas('users', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        $todos = $query->get();

        foreach ($todos as $todo) {
            $users = [];
            foreach ($todo->users as $user) {
                $users[] = $user->user_full_name;
            }

            // Determine event color based on status
            $color = '#007bff'; // default blue
            switch ($todo->status) {
                case 'Completed':
                    $color = '#28a745'; // green
                    break;
                case 'In progress':
                    $color = '#ffc107'; // yellow
                    break;
                case 'Incomplete':
                    $color = '#dc3545'; // red
                    break;
                case 'on_hold':
                    $color = '#6c757d'; // gray
                    break;
                default:
                    $color = '#17a2b8'; // info blue
            }

            // Create calendar event
            $event = [
                'id' => $todo->id,
                'title' => $todo->task,
                'start' => $todo->date ? date('Y-m-d', strtotime($todo->date)) : null,
                'end' => $todo->end_date ? date('Y-m-d', strtotime($todo->end_date)) : null,
                'color' => $color,
                'url' => action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'show'], [$todo->id]),
                'extendedProps' => [
                    'description' => $todo->description,
                    'priority' => $todo->priority,
                    'status' => $todo->status,
                    'assigned_to' => implode(', ', $users),
                    'assigned_by' => $todo->assigned_by ? $todo->assigned_by->user_full_name : '',
                    'task_id' => $todo->task_id,
                    'estimated_hours' => $todo->estimated_hours
                ]
            ];

            // Only add if start date exists
            if ($event['start']) {
                $events[] = $event;
            }
        }

        return response()->json($events);
    }

    /**
     * Show calendar view for todos
     *
     * @return Response
     */
    public function calendar()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            $auth_id = auth()->user()->id;
            
            if ($user_role_id == 1) {
                // Admin role - Show all users
                $users = User::forDropdown($business_id, false, false, false, true);
            } elseif ($user_role_id == 14) {
                // Sub-admin role - Show users from permitted locations only
                $permitted_locations = $currentUser->permitted_locations();
                
                if ($permitted_locations !== 'all' && !empty($permitted_locations)) {
                    // Get users from permitted locations
                    $location_users = User::where('business_id', $business_id)
                        ->user()
                        ->whereIn('location_id', $permitted_locations)
                        ->select('id', 'first_name', 'last_name', 'surname')
                        ->get();
                    
                    // Format users for dropdown
                    $users = $location_users->mapWithKeys(function ($user) {
                        $full_name = trim(($user->surname ?? '') . ' ' . ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                        return [$user->id => $full_name];
                    })->filter(function ($name) {
                        return !empty(trim($name));
                    });
                } else {
                    // If no location permissions, show only themselves
                    $current_user = User::find($auth_id);
                    if ($current_user) {
                        $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                        $users = [$auth_id => $full_name];
                    }
                }
            } else {
                // Other roles - Show only themselves
                $current_user = User::find($auth_id);
                if ($current_user) {
                    $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                    $users = [$auth_id => $full_name];
                }
            }
        }

        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        return view('essentials::todo.calendar')->with(compact('users', 'task_statuses', 'priorities'));
    }
}
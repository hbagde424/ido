<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsLeave;
use Modules\Essentials\Entities\EssentialsLeaveType;
use Modules\Essentials\Notifications\LeaveStatusNotification;
use Modules\Essentials\Notifications\NewLeaveNotification;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class EssentialsLeaveController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    protected $leave_statuses;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->leave_statuses = [
            'pending' => [
                'name' => __('lang_v1.pending'),
                'class' => 'bg-yellow',
            ],
            'approved' => [
                'name' => __('essentials::lang.approved'),
                'class' => 'bg-green',
            ],
            'cancelled' => [
                'name' => __('essentials::lang.cancelled'),
                'class' => 'bg-red',
            ],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $can_crud_all_leave = auth()->user()->can('essentials.crud_all_leave');
        $can_crud_own_leave = auth()->user()->can('essentials.crud_own_leave');

        if (! $can_crud_all_leave && ! $can_crud_own_leave) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $leaves = EssentialsLeave::where('essentials_leaves.business_id', $business_id)
                        ->join('users as u', 'u.id', '=', 'essentials_leaves.user_id')
                        ->join('essentials_leave_types as lt', 'lt.id', '=', 'essentials_leaves.essentials_leave_type_id')
                        ->select([
                            'essentials_leaves.id',
                            DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                            'lt.leave_type',
                            'start_date',
                            'end_date',
                            'ref_no',
                            'essentials_leaves.status',
                            'essentials_leaves.business_id',
                            'reason',
                            'status_note',
                        ]);

            if (! empty(request()->input('user_id'))) {
                $leaves->where('essentials_leaves.user_id', request()->input('user_id'));
            }

            if (! $can_crud_all_leave && $can_crud_own_leave) {
                $leaves->where('essentials_leaves.user_id', auth()->user()->id);
            }

            if (! empty(request()->input('status'))) {
                $leaves->where('essentials_leaves.status', request()->input('status'));
            }

            if (! empty(request()->input('leave_type'))) {
                $leaves->where('essentials_leaves.essentials_leave_type_id', request()->input('leave_type'));
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $leaves->whereDate('essentials_leaves.start_date', '>=', $start)
                            ->whereDate('essentials_leaves.start_date', '<=', $end);
            }

            return Datatables::of($leaves)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('essentials.crud_all_leave')) {
                            $html .= '<button class="btn btn-xs btn-danger delete-leave" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'destroy'], [$row->id]).'"><i class="fa fa-trash"></i> '.__('messages.delete').'</button>';
                        }

                        $html .= '&nbsp;<button class="btn btn-xs btn-info btn-modal" data-container=".view_modal"  data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'activity'], [$row->id]).'"><i class="fa fa-edit"></i> '.__('essentials::lang.activity').'</button>';

                        return $html;
                    }
                )
                ->editColumn('start_date', function ($row) {
                    $start_date = \Carbon::parse($row->start_date);
                    $end_date = \Carbon::parse($row->end_date);

                    $diff = $start_date->diffInDays($end_date);
                    $diff += 1;
                    $start_date_formated = $this->moduleUtil->format_date($start_date);
                    $end_date_formated = $this->moduleUtil->format_date($end_date);

                    return $start_date_formated.' - '.$end_date_formated.' ('.$diff.\Str::plural(__('lang_v1.day'), $diff).')';
                })
                ->editColumn('status', function ($row) {
                    $status = '<span class="label '.$this->leave_statuses[$row->status]['class'].'">'
                    .$this->leave_statuses[$row->status]['name'].'</span>';

                    if (auth()->user()->can('essentials.crud_all_leave') || auth()->user()->can('essentials.approve_leave')) {
                        $status = '<a href="#" class="change_status" data-status_note="'.$row->status_note.'" data-leave-id="'.$row->id.'" data-orig-value="'.$row->status.'" data-status-name="'.$this->leave_statuses[$row->status]['name'].'"> '.$status.'</a>';
                    }

                    return $status;
                })
                ->filterColumn('user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $users = [];
        if ($can_crud_all_leave || auth()->user()->can('essentials.approve_leave')) {
            $users = User::forDropdown($business_id, false);
        }
        $leave_statuses = $this->leave_statuses;

        $leave_types = EssentialsLeaveType::forDropdown($business_id);
        
        // Get quick leave balance for current user
        $quick_balance = $this->getQuickLeaveBalance();

        return view('essentials::leave.index')->with(compact('leave_statuses', 'users', 'leave_types', 'quick_balance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        $leave_types = EssentialsLeaveType::forDropdown($business_id);

        $settings = request()->session()->get('business.essentials_settings');
        $settings = ! empty($settings) ? json_decode($settings, true) : [];

        $instructions = ! empty($settings['leave_instructions']) ? $settings['leave_instructions'] : '';

        $employees = [];
        if (auth()->user()->can('essentials.crud_all_leave')) {
            $employees = User::forDropdown($business_id, false, false, false, true);
        }

        return view('essentials::leave.create')->with(compact('leave_types', 'instructions', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $can_crud_all_leave = auth()->user()->can('essentials.crud_all_leave');
        $can_crud_own_leave = auth()->user()->can('essentials.crud_own_leave');

        if (! $can_crud_all_leave && ! $can_crud_own_leave) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['essentials_leave_type_id', 'start_date', 'end_date', 'reason']);

            $input['business_id'] = $business_id;
            $input['status'] = 'pending';
            $input['start_date'] = $this->moduleUtil->uf_date($input['start_date']);
            $input['end_date'] = $this->moduleUtil->uf_date($input['end_date']);

            DB::beginTransaction();
            if (auth()->user()->can('essentials.crud_all_leave') && ! empty($request->input('employees'))) {
                foreach ($request->input('employees') as $user_id) {
                    $this->__addLeave($input, $user_id);
                }
            } else {
                $this->__addLeave($input);
            }

            DB::commit();

            $output = ['success' => true,
                'msg' => __('lang_v1.added_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    private function __addLeave($input, $user_id = null)
    {
        $input['user_id'] = ! empty($user_id) ? $user_id : request()->session()->get('user.id');
        
        // Check leave quota before creating leave
        $quota_check = $this->checkLeaveQuota($input['user_id'], $input['essentials_leave_type_id'], $input['start_date'], $input['end_date'], $input['business_id']);
        
        if (!$quota_check['available']) {
            throw new \Exception($quota_check['message']);
        }
        
        //Update reference count
        $ref_count = $this->moduleUtil->setAndGetReferenceCount('leave');
        //Generate reference number
        if (empty($input['ref_no'])) {
            $settings = request()->session()->get('business.essentials_settings');
            $settings = ! empty($settings) ? json_decode($settings, true) : [];
            $prefix = ! empty($settings['leave_ref_no_prefix']) ? $settings['leave_ref_no_prefix'] : '';
            $input['ref_no'] = $this->moduleUtil->generateReferenceNumber('leave', $ref_count, null, $prefix);
        }

        // Calculate leave days
        $start_date = \Carbon\Carbon::parse($input['start_date']);
        $end_date = \Carbon\Carbon::parse($input['end_date']);
        $input['leave_days'] = $start_date->diffInDays($end_date) + 1;

        $leave = EssentialsLeave::create($input);

        $admins = $this->moduleUtil->get_admins($input['business_id']);

        \Notification::send($admins, new NewLeaveNotification($leave));
    }

    /**
     * Check if user has enough leave quota available for the requested leave
     *
     * @param int $user_id
     * @param int $leave_type_id
     * @param string $start_date
     * @param string $end_date
     * @param int $business_id
     * @return array
     */
    private function checkLeaveQuota($user_id, $leave_type_id, $start_date, $end_date, $business_id)
    {
        // Early debug logging to confirm method is called
        \Log::info('checkLeaveQuota method called', [
            'user_id' => $user_id,
            'leave_type_id' => $leave_type_id,  
            'start_date' => $start_date,
            'end_date' => $end_date,
            'business_id' => $business_id
        ]);
        
        // Get leave type details
        $leave_type = EssentialsLeaveType::where('business_id', $business_id)
                                        ->find($leave_type_id);
        
        if (!$leave_type) {
            return [
                'available' => false,
                'message' => 'Invalid leave type selected.'
            ];
        }

        // If no max leave count set, allow unlimited leaves
        if (empty($leave_type->max_leave_count)) {
            return [
                'available' => true,
                'message' => 'Unlimited leave available.'
            ];
        }

        // Calculate requested leave days
        $start = \Carbon\Carbon::parse($start_date);
        $end = \Carbon\Carbon::parse($end_date);
        $requested_days = $start->diffInDays($end) + 1;

        // Check if this leave type has monthly limits (dual-quota system)
        $has_monthly_limit = !empty($leave_type->max_leaves_per_month);
        
        // Debug logging
        \Log::info('Quota Check Debug', [
            'user_id' => $user_id,
            'leave_type' => $leave_type->leave_type,
            'has_monthly_limit' => $has_monthly_limit,
            'max_leaves_per_month' => $leave_type->max_leaves_per_month,
            'requested_days' => $requested_days,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
        
        if ($has_monthly_limit) {
            // For dual-quota system: Check both yearly and monthly limits
            
            // 1. Check yearly quota first
            $year_start = $start->copy()->startOfYear()->format('Y-m-d');
            $year_end = $start->copy()->endOfYear()->format('Y-m-d');
            
            $yearly_leaves = EssentialsLeave::where('business_id', $business_id)
                                          ->where('user_id', $user_id)
                                          ->where('essentials_leave_type_id', $leave_type_id)
                                          ->whereIn('status', ['approved', 'pending'])
                                          ->where(function($q) use ($year_start, $year_end) {
                                              $q->whereBetween('start_date', [$year_start, $year_end])
                                                ->orWhereBetween('end_date', [$year_start, $year_end])
                                                ->orWhere(function($qq) use ($year_start, $year_end) {
                                                    $qq->where('start_date', '<=', $year_start)
                                                       ->where('end_date', '>=', $year_end);
                                                });
                                          })
                                          ->get();
            
            $yearly_used_days = 0;
            foreach ($yearly_leaves as $leave) {
                $leave_start = \Carbon\Carbon::parse($leave->start_date);
                $leave_end = \Carbon\Carbon::parse($leave->end_date);
                
                $period_start = \Carbon\Carbon::parse($year_start);
                $period_end = \Carbon\Carbon::parse($year_end);
                
                $overlap_start = $leave_start->max($period_start);
                $overlap_end = $leave_end->min($period_end);
                
                if ($overlap_start <= $overlap_end) {
                    $yearly_used_days += $overlap_start->diffInDays($overlap_end) + 1;
                }
            }
            
            $yearly_available = $leave_type->max_leave_count - $yearly_used_days;
            
            if ($requested_days > $yearly_available) {
                return [
                    'available' => false,
                    'message' => "Insufficient {$leave_type->leave_type} yearly quota. You have {$yearly_available} days remaining for this year, but requested {$requested_days} days."
                ];
            }
            
            // 2. Check monthly quota for each month the leave spans
            $current_date = $start->copy();
            while ($current_date <= $end) {
                $month_start = $current_date->copy()->startOfMonth()->format('Y-m-d');
                $month_end = $current_date->copy()->endOfMonth()->format('Y-m-d');
                
                $monthly_leaves = EssentialsLeave::where('business_id', $business_id)
                                                ->where('user_id', $user_id)
                                                ->where('essentials_leave_type_id', $leave_type_id)
                                                ->whereIn('status', ['approved', 'pending'])
                                                ->where(function($q) use ($month_start, $month_end) {
                                                    $q->whereBetween('start_date', [$month_start, $month_end])
                                                      ->orWhereBetween('end_date', [$month_start, $month_end])
                                                      ->orWhere(function($qq) use ($month_start, $month_end) {
                                                          $qq->where('start_date', '<=', $month_start)
                                                             ->where('end_date', '>=', $month_end);
                                                      });
                                                })
                                                ->get();
                
                $monthly_used_days = 0;
                foreach ($monthly_leaves as $leave) {
                    $leave_start = \Carbon\Carbon::parse($leave->start_date);
                    $leave_end = \Carbon\Carbon::parse($leave->end_date);
                    
                    $period_start = \Carbon\Carbon::parse($month_start);
                    $period_end = \Carbon\Carbon::parse($month_end);
                    
                    $overlap_start = $leave_start->max($period_start);
                    $overlap_end = $leave_end->min($period_end);
                    
                    if ($overlap_start <= $overlap_end) {
                        $monthly_used_days += $overlap_start->diffInDays($overlap_end) + 1;
                    }
                }
                
                // Calculate how many days of the requested leave fall in this month
                $request_month_start = max($start, \Carbon\Carbon::parse($month_start));
                $request_month_end = min($end, \Carbon\Carbon::parse($month_end));
                $request_days_in_month = $request_month_start->diffInDays($request_month_end) + 1;
                
                $monthly_available = $leave_type->max_leaves_per_month - $monthly_used_days;
                
                // Debug logging for monthly quota
                \Log::info('Monthly Quota Check', [
                    'month' => $current_date->format('F Y'),
                    'month_start' => $month_start,
                    'month_end' => $month_end,
                    'existing_leaves_count' => count($monthly_leaves),
                    'monthly_used_days' => $monthly_used_days,
                    'max_leaves_per_month' => $leave_type->max_leaves_per_month,
                    'monthly_available' => $monthly_available,
                    'request_days_in_month' => $request_days_in_month
                ]);
                
                if ($request_days_in_month > $monthly_available) {
                    $month_name = $current_date->format('F Y');
                    return [
                        'available' => false,
                        'message' => "Insufficient {$leave_type->leave_type} monthly quota for {$month_name}. You have {$monthly_available} days remaining for this month, but requested {$request_days_in_month} days for this month."
                    ];
                }
                
                $current_date->addMonth()->startOfMonth();
            }
            
            return [
                'available' => true,
                'message' => "Leave quota available. Both yearly and monthly limits satisfied.",
                'yearly_remaining' => $yearly_available - $requested_days
            ];
            
        } else {
            // Original single-quota system
            $interval = $leave_type->leave_count_interval ?? 'year';
            
            $query_start_date = null;
            $query_end_date = null;
            
            switch ($interval) {
                case 'month':
                    $query_start_date = $start->startOfMonth()->format('Y-m-d');
                    $query_end_date = $start->endOfMonth()->format('Y-m-d');
                    break;
                    
                case 'year':
                default:
                    $query_start_date = $start->startOfYear()->format('Y-m-d');
                    $query_end_date = $start->endOfYear()->format('Y-m-d');
                    break;
            }

            $existing_leaves = EssentialsLeave::where('business_id', $business_id)
                                            ->where('user_id', $user_id)
                                            ->where('essentials_leave_type_id', $leave_type_id)
                                            ->whereIn('status', ['approved', 'pending'])
                                            ->where(function($q) use ($query_start_date, $query_end_date) {
                                                $q->whereBetween('start_date', [$query_start_date, $query_end_date])
                                                  ->orWhereBetween('end_date', [$query_start_date, $query_end_date])
                                                  ->orWhere(function($qq) use ($query_start_date, $query_end_date) {
                                                      $qq->where('start_date', '<=', $query_start_date)
                                                         ->where('end_date', '>=', $query_end_date);
                                                  });
                                            })
                                            ->get();

            $used_days = 0;
            foreach ($existing_leaves as $leave) {
                $leave_start = \Carbon\Carbon::parse($leave->start_date);
                $leave_end = \Carbon\Carbon::parse($leave->end_date);
                
                $period_start = \Carbon\Carbon::parse($query_start_date);
                $period_end = \Carbon\Carbon::parse($query_end_date);
                
                $overlap_start = $leave_start->max($period_start);
                $overlap_end = $leave_end->min($period_end);
                
                if ($overlap_start <= $overlap_end) {
                    $used_days += $overlap_start->diffInDays($overlap_end) + 1;
                }
            }

            $available_days = $leave_type->max_leave_count - $used_days;
            
            if ($requested_days > $available_days) {
                $period_text = ($interval == 'month') ? 'this month' : 'this year';
                return [
                    'available' => false,
                    'message' => "Insufficient {$leave_type->leave_type} quota. You have {$available_days} days remaining for {$period_text}, but requested {$requested_days} days."
                ];
            }

            return [
                'available' => true,
                'message' => "Leave quota available. {$available_days} days remaining after this request.",
                'remaining_after' => $available_days - $requested_days
            ];
        }
    }

    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show()
    {
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit()
    {
        return view('essentials::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.crud_all_leave')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                EssentialsLeave::where('business_id', $business_id)->where('id', $id)->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.deleted_success'),
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

    public function changeStatus(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) || ! auth()->user()->can('essentials.approve_leave')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['status', 'leave_id', 'status_note']);

            $leave = EssentialsLeave::where('business_id', $business_id)
                            ->find($input['leave_id']);

            $leave->status = $input['status'];
            $leave->status_note = $input['status_note'];
            $leave->save();

            $leave->status = $this->leave_statuses[$leave->status]['name'];

            $leave->changed_by = auth()->user()->id;

            $leave->user->notify(new LeaveStatusNotification($leave));

            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Function to show activity log related to a leave
     *
     * @return Response
     */
    public function activity($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $leave = EssentialsLeave::where('business_id', $business_id)
                                ->find($id);

        $activities = Activity::forSubject($leave)
                           ->with(['causer', 'subject'])
                           ->latest()
                           ->get();

        return view('essentials::leave.activity_modal')->with(compact('leave', 'activities'));
    }

    /**
     * Function to get leave summary of a user
     *
     * @return Response
     */
    public function getUserLeaveSummary()
    {
        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $user_id = $is_admin ? request()->input('user_id') : auth()->user()->id;

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (empty($user_id)) {
            return '';
        }

        $query = EssentialsLeave::where('business_id', $business_id)
                            ->where('user_id', $user_id)
                            ->with(['leave_type'])
                            ->select(
                                'status',
                                'essentials_leave_type_id',
                                'start_date',
                                'end_date'
                            );

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $query->whereDate('start_date', '>=', $start)
                        ->whereDate('start_date', '<=', $end);
        }
        $leaves = $query->get();
        $statuses = $this->leave_statuses;
        $leaves_summary = [];
        $status_summary = [];
        $remaining_leaves = [];

        foreach ($statuses as $key => $value) {
            $status_summary[$key] = 0;
        }
        foreach ($leaves as $leave) {
            $start_date = \Carbon::parse($leave->start_date);
            $end_date = \Carbon::parse($leave->end_date);
            $diff = $start_date->diffInDays($end_date) + 1;

            $leaves_summary[$leave->essentials_leave_type_id][$leave->status] =
            isset($leaves_summary[$leave->essentials_leave_type_id][$leave->status]) ?
            $leaves_summary[$leave->essentials_leave_type_id][$leave->status] + $diff : $diff;

            $status_summary[$leave->status] = isset($status_summary[$leave->status]) ? ($status_summary[$leave->status] + $diff) : $diff;
        }

        $leave_types = EssentialsLeaveType::where('business_id', $business_id)
                                    ->get();
        
        // Calculate remaining leaves for each leave type
        foreach ($leave_types as $leave_type) {
            if (!empty($leave_type->max_leave_count)) {
                $used_leaves = 0;
                
                // Calculate used leaves (approved + pending)
                if (!empty($leaves_summary[$leave_type->id]['approved'])) {
                    $used_leaves += $leaves_summary[$leave_type->id]['approved'];
                }
                if (!empty($leaves_summary[$leave_type->id]['pending'])) {
                    $used_leaves += $leaves_summary[$leave_type->id]['pending'];
                }
                
                $remaining = $leave_type->max_leave_count - $used_leaves;
                $remaining_leaves[$leave_type->id] = max(0, $remaining); // Don't allow negative remaining
            } else {
                $remaining_leaves[$leave_type->id] = 'Unlimited';
            }
        }
        
        $user = User::where('business_id', $business_id)
                    ->find($user_id);

        return view('essentials::leave.user_leave_summary')->with(compact('leaves_summary', 'leave_types', 'statuses', 'user', 'status_summary', 'remaining_leaves'));
    }

    /**
     * Get quick leave balance for current user or specified user
     *
     * @return array
     */
    public function getQuickLeaveBalance()
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->input('user_id', auth()->user()->id);
        
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return [];
        }

        // Get current year/month based leave data
        $current_year = date('Y');
        $current_month = date('m');
        
        $leaves = EssentialsLeave::where('business_id', $business_id)
                    ->where('user_id', $user_id)
                    ->whereYear('start_date', $current_year)
                    ->whereIn('status', ['approved', 'pending'])
                    ->with(['leave_type'])
                    ->get();
        
        $leave_types = EssentialsLeaveType::where('business_id', $business_id)->get();
        
        $balance = [];
        foreach ($leave_types as $leave_type) {
            $used = 0;
            foreach ($leaves as $leave) {
                if ($leave->essentials_leave_type_id == $leave_type->id) {
                    $start_date = \Carbon::parse($leave->start_date);
                    $end_date = \Carbon::parse($leave->end_date);
                    $used += $start_date->diffInDays($end_date) + 1;
                }
            }
            
            $remaining = 'Unlimited';
            if (!empty($leave_type->max_leave_count)) {
                $remaining = max(0, $leave_type->max_leave_count - $used);
            }
            
            $balance[] = [
                'leave_type' => $leave_type->leave_type,
                'max_allowed' => $leave_type->max_leave_count ?? 'Unlimited',
                'used' => $used,
                'remaining' => $remaining
            ];
        }
        
        // Get user information
        $user = User::where('business_id', $business_id)->find($user_id);
        
        // If AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json([
                'balance' => $balance,
                'user' => $user ? $user->user_full_name : 'Unknown User',
                'user_id' => $user_id
            ]);
        }
        
        return $balance;
    }

    /**
     * Get quick leave balance view for AJAX
     *
     * @return Response
     */
    public function getQuickLeaveBalanceView()
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->input('user_id');
        
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $balance = [];
        $user_name = '';
        
        if ($user_id) {
            // Get user name
            $user = User::where('business_id', $business_id)->find($user_id);
            $user_name = $user ? $user->user_full_name : 'Unknown User';
            
            // Get current year leave data
            $current_year = date('Y');
            
            $leaves = EssentialsLeave::where('business_id', $business_id)
                        ->where('user_id', $user_id)
                        ->whereYear('start_date', $current_year)
                        ->whereIn('status', ['approved', 'pending'])
                        ->with(['leave_type'])
                        ->get();
            
            $leave_types = EssentialsLeaveType::where('business_id', $business_id)->get();
            
            foreach ($leave_types as $leave_type) {
                $used = 0;
                foreach ($leaves as $leave) {
                    if ($leave->essentials_leave_type_id == $leave_type->id) {
                        $start_date = \Carbon::parse($leave->start_date);
                        $end_date = \Carbon::parse($leave->end_date);
                        $used += $start_date->diffInDays($end_date) + 1;
                    }
                }
                
                $remaining = 'Unlimited';
                if (!empty($leave_type->max_leave_count)) {
                    $remaining = max(0, $leave_type->max_leave_count - $used);
                }
                
                $balance[] = [
                    'leave_type' => $leave_type->leave_type,
                    'max_allowed' => $leave_type->max_leave_count ?? 'Unlimited',
                    'used' => $used,
                    'remaining' => $remaining
                ];
            }
        }
        
        return view('essentials::leave.quick_leave_balance')->with(compact('balance', 'user_name'));
    }

    /**
     * Check leave quota availability via AJAX
     *
     * @param Request $request
     * @return Response
     */
    public function checkQuota(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            return response()->json(['available' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $user_id = $request->input('user_id', auth()->user()->id);
        $leave_type_id = $request->input('leave_type_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if (!$leave_type_id || !$start_date || !$end_date) {
            return response()->json([
                'available' => false,
                'message' => 'Please provide all required fields.'
            ]);
        }

        try {
            $start_date = $this->moduleUtil->uf_date($start_date);
            $end_date = $this->moduleUtil->uf_date($end_date);
            
            $quota_check = $this->checkLeaveQuota($user_id, $leave_type_id, $start_date, $end_date, $business_id);
            
            return response()->json($quota_check);
            
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => 'Error checking quota: ' . $e->getMessage()
            ]);
        }
    }
}

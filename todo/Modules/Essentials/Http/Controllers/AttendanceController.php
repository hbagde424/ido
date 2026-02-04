<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsAttendance;
use Modules\Essentials\Entities\Shift;
use Modules\Essentials\Utils\EssentialsUtil;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    protected $essentialsUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, EssentialsUtil $essentialsUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->essentialsUtil = $essentialsUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
   public function index()
{
    $business_id = request()->session()->get('user.business_id');

    // Permission and subscription check
    if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
        abort(403, 'Unauthorized action.');
    }

    $can_crud_all_attendance = auth()->user()->can('essentials.crud_all_attendance');
    $can_view_own_attendance = auth()->user()->can('essentials.view_own_attendance');

    if (!$can_crud_all_attendance && !$can_view_own_attendance) {
        abort(403, 'Unauthorized action.');
    }

    if (request()->ajax()) {
        $attendance = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
            ->leftJoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
            ->leftJoin('categories as dept', function ($join) {
                $join->on('dept.id', '=', 'u.essentials_department_id')
                    ->where('dept.category_type', '=', 'hrm_department');
            })
            ->leftJoin('business_locations as bl', 'bl.id', '=', 'u.location_id')
            ->select([
                'essentials_attendances.id',
                'clock_in_time',
                'clock_out_time',
                'clock_in_note',
                'clock_out_note',
                'ip_address',
                DB::raw('DATE(clock_in_time) as date'),
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                DB::raw("COALESCE(dept.name, 'NA') as department"),
                DB::raw("COALESCE(bl.name, 'NA') as location"),
                'es.name as shift_name',
                'clock_in_location',
                'clock_out_location',
                'u.location_id'
            ]);

        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $attendance->whereIn('u.location_id', $permitted_locations);
        }

        if (!empty(request()->input('employee_id'))) {
            $attendance->where('essentials_attendances.user_id', request()->input('employee_id'));
        }

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $attendance->whereDate('clock_in_time', '>=', $start)
                ->whereDate('clock_in_time', '<=', $end);
        }

        if (!$can_crud_all_attendance && $can_view_own_attendance) {
            $attendance->where('essentials_attendances.user_id', auth()->user()->id);
        }

        return Datatables::of($attendance)
            ->addColumn('action', '@can("essentials.crud_all_attendance") 
                <button data-href="{{action(\'\\Modules\\Essentials\\Http\\Controllers\\AttendanceController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container="#edit_attendance_modal">
                    <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
                </button>
                <button class="btn btn-xs btn-danger delete-attendance" data-href="{{action(\'\\Modules\\Essentials\\Http\\Controllers\\AttendanceController@destroy\', [$id])}}">
                    <i class="fa fa-trash"></i> @lang("messages.delete")
                </button> 
            @endcan')
            ->editColumn('work_duration', function ($row) {
                $clock_in = \Carbon::parse($row->clock_in_time);
                $clock_out = !empty($row->clock_out_time) ? \Carbon::parse($row->clock_out_time) : \Carbon::now();
                return $clock_in->diffForHumans($clock_out, true, true, 2);
            })
            ->editColumn('clock_in', function ($row) {
                $html = $row->clock_in_time;
                if (!empty($row->clock_in_location)) {
                    $html .= '<br>' . $row->clock_in_location . '<br>';
                }
                if (!empty($row->clock_in_note)) {
                    $html .= '<br>' . $row->clock_in_note . '<br>';
                }
                return $html;
            })
            ->editColumn('clock_out', function ($row) {
                $html = $row->clock_out_time;
                if (!empty($row->clock_out_location)) {
                    $html .= '<br>' . $row->clock_out_location . '<br>';
                }
                if (!empty($row->clock_out_note)) {
                    $html .= '<br>' . $row->clock_out_note . '<br>';
                }
                return $html;
            })
            ->editColumn('date', '{{@format_date($date)}}')
            ->addColumn('department', function ($row) {
                return $row->department ?? 'NA';
            })
            ->addColumn('location', function ($row) {
                return $row->location ?? 'NA';
            })
            ->rawColumns(['action', 'clock_in', 'work_duration', 'clock_out', 'department', 'location'])
            ->filterColumn('user', function ($query, $keyword) {
                $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    // Non-AJAX: page load
    $settings = request()->session()->get('business.essentials_settings');
    $settings = !empty($settings) ? json_decode($settings, true) : [];

    $is_employee_allowed = auth()->user()->can('essentials.allow_users_for_attendance_from_web');
    $clock_in = EssentialsAttendance::where('business_id', $business_id)
        ->where('user_id', auth()->user()->id)
        ->whereNull('clock_out_time')
        ->first();

    $employees = [];
    // Allow calendar view for users with either crud_all_attendance or view_own_attendance permission
    if ($can_crud_all_attendance || $can_view_own_attendance) {
        // Role-based employee filtering for calendar view
        $currentUser = auth()->user();
        $user_role_id = $currentUser->roleId ?? 0;
        $auth_id = auth()->user()->id;
        
        if ($user_role_id == 1 && $can_crud_all_attendance) {
            // Admin role with full access - Show all users
            $employees = User::forDropdown($business_id, false, false, false, true);
        } elseif ($user_role_id == 14 && $can_crud_all_attendance) {
            // Sub-admin role with full access - Show users from permitted locations only
            // Exclude admin users
            $util = new \App\Utils\Util();
            $admin_user_ids = $util->getAdminUserIds($business_id);
            
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
                // Get users from permitted locations, excluding admins
                $location_users = User::where('business_id', $business_id)
                    ->user()
                    ->whereIn('location_id', $permitted_locations)
                    ->whereNotIn('id', $admin_user_ids)
                    ->select('id', 'first_name', 'last_name', 'surname')
                    ->get();
                
                // Format users for dropdown
                $employees = $location_users->mapWithKeys(function ($user) {
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
                    $employees = [$auth_id => $full_name];
                }
            }
        } else {
            // Regular users or users with only view_own_attendance - Show only themselves
            $current_user = User::find($auth_id);
            if ($current_user) {
                $full_name = trim(($current_user->surname ?? '') . ' ' . ($current_user->first_name ?? '') . ' ' . ($current_user->last_name ?? ''));
                $employees = [$auth_id => $full_name];
            }
        }
    }

    $days = $this->moduleUtil->getDays();

    return view('essentials::attendance.index')
        ->with(compact('is_employee_allowed', 'clock_in', 'employees', 'days'));
}


    /**
     * Export attendance data with Sunday rows included
     */
    public function exportAttendance()
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $can_crud_all_attendance = auth()->user()->can('essentials.crud_all_attendance');
            $can_view_own_attendance = auth()->user()->can('essentials.view_own_attendance');

            if (!$can_crud_all_attendance && !$can_view_own_attendance) {
                abort(403, 'Unauthorized action.');
            }

            // Debug logging for export parameters
            \Log::info('Export parameters:', [
                'employee_id' => request()->input('employee_id'),
                'start_date' => request()->input('start_date'),
                'end_date' => request()->input('end_date'),
                'format' => request()->input('format')
            ]);

        // Get the same query as the main index method
        $attendance = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
            ->leftJoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
            ->leftJoin('categories as dept', function ($join) {
                $join->on('dept.id', '=', 'u.essentials_department_id')
                    ->where('dept.category_type', '=', 'hrm_department');
            })
            ->leftJoin('model_has_permissions as mhp', 'mhp.model_id', '=', 'u.id')
            ->leftJoin('permissions as p', 'p.id', '=', 'mhp.permission_id')
            ->leftJoin('business_locations as bl', function($join) {
                $join->on(DB::raw("CAST(REPLACE(p.name, 'location.', '') AS UNSIGNED)"), '=', 'bl.id')
                    ->where('p.name', 'like', 'location.%');
            })
            ->select([
                'essentials_attendances.id',
                'clock_in_time',
                'clock_out_time',
                'clock_in_note',
                'clock_out_note',
                'ip_address',
                DB::raw('DATE(clock_in_time) as date'),
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                DB::raw("COALESCE(dept.name, 'NA') as department"),
                DB::raw("COALESCE(GROUP_CONCAT(DISTINCT bl.name SEPARATOR ', '), 'NA') as location"),
                'es.name as shift_name',
                'clock_in_location',
                'clock_out_location',
                'essentials_attendances.user_id',
            ])
            ->groupBy('essentials_attendances.id');

        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations != 'all') {
            $attendance->whereIn(DB::raw("CAST(REPLACE(p.name, 'location.', '') AS UNSIGNED)"), $permitted_locations);
        }

        if (!empty(request()->input('employee_id'))) {
            $attendance->where('essentials_attendances.user_id', request()->input('employee_id'));
        }

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $attendance->whereDate('clock_in_time', '>=', $start)
                ->whereDate('clock_in_time', '<=', $end);
        } else {
            // If no date range specified, use last 30 days as default (same as main list)
            $default_start = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
            $default_end = \Carbon\Carbon::now()->format('Y-m-d');
            $attendance->whereDate('clock_in_time', '>=', $default_start)
                ->whereDate('clock_in_time', '<=', $default_end);
        }

        if (!$can_crud_all_attendance && $can_view_own_attendance) {
            $attendance->where('essentials_attendances.user_id', auth()->user()->id);
        }

        // Get the actual attendance data
        $attendanceData = $attendance->get();
        
        // Generate Sunday rows for the date range
        $sundayRows = $this->generateSundayRowsForExport($attendanceData, $business_id);
        
        // Combine attendance data with Sunday rows
        $combinedData = $attendanceData->concat($sundayRows);
        
        // Sort by date and user
        $combinedData = $combinedData->sortBy(function($item) {
            return $item->date . '_' . $item->user;
        });

        // Format data for export
        $exportData = $combinedData->map(function($row) {
            $date = \Carbon\Carbon::parse($row->date);
            $isSunday = $date->dayOfWeek === 0 || (isset($row->is_sunday) && $row->is_sunday);
            
            return [
                'Date' => $isSunday ? $date->format('d-m-Y') . ' (Sunday)' : $date->format('d-m-Y'),
                'Employee' => $row->user,
                'Department' => $row->department ?? 'NA',
                'Location' => $row->location ?? 'NA',
                'Clock In' => $isSunday ? 'Weekend' : ($row->clock_in_time ?? 'N/A'),
                'Clock Out' => $isSunday ? 'Weekend' : ($row->clock_out_time ?? 'N/A'),
                'Work Duration' => $isSunday ? 'Weekend' : ($row->clock_in_time ? $this->calculateWorkDuration($row->clock_in_time, $row->clock_out_time) : 'N/A'),
                'IP Address' => $isSunday ? 'N/A' : ($row->ip_address ?? 'N/A'),
                'Shift' => $isSunday ? 'Weekend' : ($row->shift_name ?? 'N/A'),
            ];
        });

        $format = request()->input('format', 'csv');
        
        if ($format === 'excel') {
            return $this->exportToExcel($exportData);
        } else {
            return $this->exportToCSV($exportData);
        }
        
        } catch (\Exception $e) {
            \Log::error('Export attendance error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Sunday rows for export
     */
    private function generateSundayRowsForExport($attendanceData, $business_id)
    {
        $sundayRows = collect();
        
        // Get the date range from request
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $employee_id = request()->input('employee_id');
        
        // If no date range specified, use last 30 days
        if (!$start_date || !$end_date) {
            $start_date = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
            $end_date = \Carbon\Carbon::now()->format('Y-m-d');
        }
        
        // Get users to generate Sunday rows for
        $users = collect();
        if ($employee_id) {
            // If specific employee selected, only generate for that employee
            $user = \App\User::find($employee_id);
            if ($user) {
                $users->push($user);
            }
        } else {
            // If no specific employee, get all users for the business
            $users = \App\User::where('business_id', $business_id)->user()->get();
        }
        
        // Generate Sunday rows for each user in the date range
        foreach ($users as $user) {
            $start = \Carbon\Carbon::parse($start_date);
            $end = \Carbon\Carbon::parse($end_date);
            
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                // Check if it's Sunday
                if ($date->dayOfWeek === 0) { // 0 = Sunday
                    // Check if there's already an attendance record for this date and user
                    $hasAttendance = $attendanceData->where('user_id', $user->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->count() > 0;
                    
                    // Only add Sunday row if there's no existing attendance record
                    if (!$hasAttendance) {
                        $sundayRow = (object) [
                            'id' => 'sunday_' . $user->id . '_' . $date->format('Y-m-d'),
                            'date' => $date->format('Y-m-d'),
                            'user' => trim($user->surname . ' ' . $user->first_name . ' ' . $user->last_name),
                            'department' => 'NA',
                            'location' => 'NA',
                            'clock_in_time' => null,
                            'clock_out_time' => null,
                            'clock_in_note' => null,
                            'clock_out_note' => null,
                            'ip_address' => 'N/A',
                            'shift_name' => 'Weekend',
                            'clock_in_location' => null,
                            'clock_out_location' => null,
                            'user_id' => $user->id,
                            'is_sunday' => true
                        ];
                        
                        $sundayRows->push($sundayRow);
                    }
                }
            }
        }
        
        return $sundayRows;
    }

    /**
     * Calculate work duration for export
     */
    private function calculateWorkDuration($clock_in_time, $clock_out_time)
    {
        if (empty($clock_in_time)) {
            return 'N/A';
        }
        
        $clock_in = \Carbon\Carbon::parse($clock_in_time);
        $clock_out = !empty($clock_out_time) ? \Carbon\Carbon::parse($clock_out_time) : \Carbon\Carbon::now();
        return $clock_in->diffForHumans($clock_out, true, true, 2);
    }

    /**
     * Export data to Excel format
     */
    private function exportToExcel($data)
    {
        $filename = 'attendance_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Create a simple Excel file using basic PHP
        $excelData = "Date\tEmployee\tDepartment\tLocation\tClock In\tClock Out\tWork Duration\tIP Address\tShift\n";
        
        foreach ($data as $row) {
            $excelData .= $row['Date'] . "\t";
            $excelData .= $row['Employee'] . "\t";
            $excelData .= $row['Department'] . "\t";
            $excelData .= $row['Location'] . "\t";
            $excelData .= $row['Clock In'] . "\t";
            $excelData .= $row['Clock Out'] . "\t";
            $excelData .= $row['Work Duration'] . "\t";
            $excelData .= $row['IP Address'] . "\t";
            $excelData .= $row['Shift'] . "\n";
        }
        
        return response($excelData)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    /**
     * Export data to CSV format
     */
    private function exportToCSV($data)
    {
        $filename = 'attendance_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $csvData = "Date,Employee,Department,Location,Clock In,Clock Out,Work Duration,IP Address,Shift\n";
        
        foreach ($data as $row) {
            $csvData .= '"' . $row['Date'] . '",';
            $csvData .= '"' . $row['Employee'] . '",';
            $csvData .= '"' . $row['Department'] . '",';
            $csvData .= '"' . $row['Location'] . '",';
            $csvData .= '"' . $row['Clock In'] . '",';
            $csvData .= '"' . $row['Clock Out'] . '",';
            $csvData .= '"' . $row['Work Duration'] . '",';
            $csvData .= '"' . $row['IP Address'] . '",';
            $csvData .= '"' . $row['Shift'] . '"' . "\n";
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    /**
     * Test export method
     */
    public function testExport()
    {
        return response()->json([
            'message' => 'Export method is working',
            'url' => route('attendance.export'),
            'timestamp' => now()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $employees = User::forDropdown($business_id, false, false, false, true);

        return view('essentials::attendance.create')->with(compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $attendance = $request->input('attendance');
            $ip_address = $this->moduleUtil->getUserIpAddr();
            if (! empty($attendance)) {
                foreach ($attendance as $user_id => $value) {
                    $data = [
                        'business_id' => $business_id,
                        'user_id' => $user_id,
                    ];

                    if (! empty($value['clock_in_time'])) {
                        $data['clock_in_time'] = $this->moduleUtil->uf_date($value['clock_in_time'], true);
                    }
                    if (! empty($value['id'])) {
                        $data['id'] = $value['id'];
                    }
                    EssentialsAttendance::updateOrCreate(
                        $data,
                        [
                            'clock_out_time' => ! empty($value['clock_out_time']) ? $this->moduleUtil->uf_date($value['clock_out_time'], true) : null,
                            'ip_address' => ! empty($value['ip_address']) ? $value['ip_address'] : $ip_address,
                            'clock_in_note' => $value['clock_in_note'],
                            'clock_out_note' => $value['clock_out_note'],
                            'essentials_shift_id' => $value['essentials_shift_id'],
                        ]
                    );
                }
            }

            $output = ['success' => true,
                'msg' => __('lang_v1.added_success'),
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
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = EssentialsAttendance::where('business_id', $business_id)
                                    ->with(['employee'])
                                    ->find($id);

        return view('essentials::attendance.edit')->with(compact('attendance'));
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
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['clock_in_time', 'clock_out_time', 'ip_address', 'clock_in_note', 'clock_out_note']);

            $input['clock_in_time'] = $this->moduleUtil->uf_date($input['clock_in_time'], true);
            $input['clock_out_time'] = ! empty($input['clock_out_time']) ? $this->moduleUtil->uf_date($input['clock_out_time'], true) : null;

            $attendance = EssentialsAttendance::where('business_id', $business_id)
                                            ->where('id', $id)
                                            ->update($input);
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
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                EssentialsAttendance::where('business_id', $business_id)->where('id', $id)->delete();

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

    /**
     * Clock in / Clock out the logged in user.
     *
     * @return Response
     */
    public function clockInClockOut(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        //Check if employees allowed to add their own attendance
        $settings = request()->session()->get('business.essentials_settings');
        $settings = ! empty($settings) ? json_decode($settings, true) : [];
        if (! auth()->user()->can('essentials.allow_users_for_attendance_from_web')) {
            return ['success' => false,
                'msg' => __('essentials::lang.not_allowed'),
            ];
        } elseif ((! empty($settings['is_location_required']) && $settings['is_location_required']) && empty($request->input('clock_in_out_location'))) {
            return ['success' => false,
                'msg' => __('essentials::lang.you_must_enable_location'),
            ];
        }

        try {
            $type = $request->input('type');

            if ($type == 'clock_in') {
                $data = [
                    'business_id' => $business_id,
                    'user_id' => auth()->user()->id,
                    'clock_in_time' => \Carbon::now(),
                    'clock_in_note' => $request->input('clock_in_note'),
                    'ip_address' => $this->moduleUtil->getUserIpAddr(),
                    'clock_in_location' => $request->input('clock_in_out_location'),
                ];

                $output = $this->essentialsUtil->clockin($data, $settings);
            } elseif ($type == 'clock_out') {
                $data = [
                    'business_id' => $business_id,
                    'user_id' => auth()->user()->id,
                    'clock_out_time' => \Carbon::now(),
                    'clock_out_note' => $request->input('clock_out_note'),
                    'clock_out_location' => $request->input('clock_in_out_location'),
                ];

                $output = $this->essentialsUtil->clockout($data, $settings);
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
                'type' => $type,
            ];
        }

        return $output;
    }

    /**
     * Function to get attendance summary of a user
     *
     * @return Response
     */
    public function getUserAttendanceSummary()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        $user_id = $is_admin ? request()->input('user_id') : auth()->user()->id;

        if (empty($user_id)) {
            return '';
        }

        $start_date = ! empty(request()->start_date) ? request()->start_date : null;
        $end_date = ! empty(request()->end_date) ? request()->end_date : null;

        $total_work_duration = $this->essentialsUtil->getTotalWorkDuration('hour', $user_id, $business_id, $start_date, $end_date);

        return $total_work_duration;
    }

    /**
     * Function to validate clock in and clock out time
     *
     * @return string
     */
    public function validateClockInClockOut(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_ids = explode(',', $request->input('user_ids'));
        $clock_in_time = $request->input('clock_in_time');
        $clock_out_time = $request->input('clock_out_time');
        $attendance_id = $request->input('attendance_id');

        $is_valid = 'true';
        if (! empty($user_ids)) {

            //Check if clock in time falls under any existing attendance range
            $is_clock_in_exists = false;
            if (! empty($clock_in_time)) {
                $clock_in_time = $this->essentialsUtil->uf_date($clock_in_time, true);

                $is_clock_in_exists = EssentialsAttendance::where('business_id', $business_id)
                                        ->where('id', '!=', $attendance_id)
                                        ->whereIn('user_id', $user_ids)
                                        ->where('clock_in_time', '<', $clock_in_time)
                                        ->where('clock_out_time', '>', $clock_in_time)
                                        ->exists();
            }

            //Check if clock out time falls under any existing attendance range
            $is_clock_out_exists = false;
            if (! empty($clock_out_time)) {
                $clock_out_time = $this->essentialsUtil->uf_date($clock_out_time, true);

                $is_clock_out_exists = EssentialsAttendance::where('business_id', $business_id)
                                        ->where('id', '!=', $attendance_id)
                                        ->whereIn('user_id', $user_ids)
                                        ->where('clock_in_time', '<', $clock_out_time)
                                        ->where('clock_out_time', '>', $clock_out_time)
                                        ->exists();
            }

            if ($is_clock_in_exists || $is_clock_out_exists) {
                $is_valid = 'false';
            }
        }

        return $is_valid;
    }

    /**
     * Get attendance summary by shift
     */
    public function getAttendanceByShift()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        $date = $this->moduleUtil->uf_date(request()->input('date'));

        $attendance_data = EssentialsAttendance::where('business_id', $business_id)
                                ->whereDate('clock_in_time', $date)
                                ->whereNotNull('essentials_shift_id')
                                ->with(['shift', 'shift.user_shifts', 'shift.user_shifts.user', 'employee'])
                                ->get();
        $attendance_by_shift = [];
        $date_obj = \Carbon::parse($date);
        foreach ($attendance_data as $data) {
            if (empty($attendance_by_shift[$data->essentials_shift_id])) {
                //Calculate total users in the shift
                $total_users = 0;
                $all_users = [];
                foreach ($data->shift->user_shifts as $user_shift) {
                    if (! empty($user_shift->start_date) && ! empty($user_shift->end_date) && $date_obj->between(\Carbon::parse($user_shift->start_date), \Carbon::parse($user_shift->end_date))) {
                        $total_users++;
                        $all_users[] = $user_shift->user->user_full_name;
                    }
                }
                $attendance_by_shift[$data->essentials_shift_id] = [
                    'present' => 1,
                    'shift' => $data->shift->name,
                    'total' => $total_users,
                    'present_users' => [$data->employee->user_full_name],
                    'all_users' => $all_users,
                ];
            } else {
                if (! in_array($data->employee->user_full_name, $attendance_by_shift[$data->essentials_shift_id]['present_users'])) {
                    $attendance_by_shift[$data->essentials_shift_id]['present']++;
                    $attendance_by_shift[$data->essentials_shift_id]['present_users'][] = $data->employee->user_full_name;
                }
            }
        }

        return view('essentials::attendance.attendance_by_shift_data')->with(compact('attendance_by_shift'));
    }

    /**
     * Get attendance summary by date
     */
    public function getAttendanceByDate()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');

        $attendance_data = EssentialsAttendance::where('business_id', $business_id)
                                ->whereDate('clock_in_time', '>=', $start_date)
                                ->whereDate('clock_in_time', '<=', $end_date)
                                ->select(
                                    'essentials_attendances.*',
                                    DB::raw('COUNT(DISTINCT essentials_attendances.user_id) as total_present'),
                                    DB::raw('CAST(clock_in_time AS DATE) as clock_in_date')
                                )
                                ->groupBy(DB::raw('CAST(clock_in_time AS DATE)'))
                                ->get();

        $all_users = User::where('business_id', $business_id)
                        ->user()
                        ->count();

        $attendance_by_date = [];
        foreach ($attendance_data as $data) {
            $total_present = ! empty($data->total_present) ? $data->total_present : 0;
            $attendance_by_date[] = [
                'present' => $total_present,
                'absent' => $all_users - $total_present,
                'date' => $data->clock_in_date,
            ];
        }

        return view('essentials::attendance.attendance_by_date_data')->with(compact('attendance_by_date'));
    }

    /**
     * Function to import attendance.
     *
     * @param  Request  $request
     * @return Response
     */
    public function importAttendance(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->moduleUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('attendance')) {
                $file = $request->file('attendance');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                $ip_address = $this->moduleUtil->getUserIpAddr();
                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 1;
                    $temp = [];

                    //Add user
                    if (! empty($value[0])) {
                        $email = trim($value[0]);
                        $user = User::where('business_id', $business_id)->where('email', $email)->first();
                        if (! empty($user)) {
                            $temp['user_id'] = $user->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "User not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "Email is required in row no. $row_no";
                        break;
                    }

                    //clockin time
                    if (! empty($value[1])) {
                        $temp['clock_in_time'] = trim($value[1]);
                    } else {
                        $is_valid = false;
                        $error_msg = "Clock in time is required in row no. $row_no";
                        break;
                    }
                    $temp['clock_out_time'] = ! empty($value[2]) ? trim($value[2]) : null;

                    //Add shift
                    if (! empty($value[3])) {
                        $shift_name = trim($value[3]);
                        $shift = Shift::where('business_id', $business_id)->where('name', $shift_name)->first();
                        if (! empty($shift)) {
                            $temp['essentials_shift_id'] = $shift->id;
                        } else {
                            $is_valid = false;
                            $error_msg = "Shift not found in row no. $row_no";
                            break;
                        }
                    }

                    $temp['clock_in_note'] = ! empty($value[4]) ? trim($value[4]) : null;
                    $temp['clock_out_note'] = ! empty($value[5]) ? trim($value[5]) : null;
                    $temp['ip_address'] = ! empty($value[6]) ? trim($value[6]) : $ip_address;
                    $temp['business_id'] = $business_id;
                    $formated_data[] = $temp;
                }

                if (! $is_valid) {
                    throw new \Exception($error_msg);
                }

                if (! empty($formated_data)) {
                    EssentialsAttendance::insert($formated_data);
                }

                $output = ['success' => 1,
                    'msg' => __('product.file_imported_successfully'),
                ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];

            return redirect()->back()->with('notification', $output);
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Adds attendance row for an employee on add latest attendance form
     *
     * @param  int  $user_id
     * @return Response
     */
    public function getAttendanceRow($user_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module') || $is_admin)) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('business_id', $business_id)
                    ->findOrFail($user_id);

        $attendance = EssentialsAttendance::where('business_id', $business_id)
                                        ->where('user_id', $user_id)
                                        ->whereNotNull('clock_in_time')
                                        ->whereNull('clock_out_time')
                                        ->first();

        $shifts = Shift::join('essentials_user_shifts as eus', 'eus.essentials_shift_id', '=', 'essentials_shifts.id')
                    ->where('essentials_shifts.business_id', $business_id)
                    ->where('eus.user_id', $user_id)
                    ->where('eus.start_date', '<=', \Carbon::now()->format('Y-m-d'))
                    ->pluck('essentials_shifts.name', 'essentials_shifts.id');

        return view('essentials::attendance.attendance_row')->with(compact('attendance', 'shifts', 'user'));
    }

    /**
     * Get calendar data for attendance view
     *
     * @param  Request  $request
     * @return Response
     */
    public function getCalendarData(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                abort(403, 'Unauthorized action.');
            }

        $start_date = $request->get('start');
        $end_date = $request->get('end');
        $employee_id = $request->get('employee_id');

        $events = [];

        // Role-based scoping (Admin: all, Sub-admin: by location, Others: own only)
        $currentUser = auth()->user();
        $user_role_id = $currentUser->roleId ?? 0;
        
        // Compute permitted locations for sub-admin style permissions (location.*)
        $permitted_locations = [];
        if ($user_role_id == 14) {
            $user_permissions = $currentUser->permissions->pluck('name')->all();
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
        }

        // Get attendance data - simplified query to avoid join issues
        $attendance_query = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
            ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
            ->leftJoin('essentials_shifts as es', 'es.id', '=', 'essentials_attendances.essentials_shift_id')
            ->select([
                'essentials_attendances.*',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as employee_name"),
                'es.name as shift_name',
                'u.id as user_id',
                'u.location_id'
            ]);

        // Apply role-based visibility
        if ($user_role_id == 1) {
            // Admin: no extra restriction - can see all attendance
        } elseif ($user_role_id == 14) {
            // Sub-admin: limit by permitted locations if any, else fallback to own
            // Exclude admin users' attendance
            $util = new \App\Utils\Util();
            $admin_user_ids = $util->getAdminUserIds($business_id);
            
            if (!empty($permitted_locations)) {
                // Filter attendance to only show those from users in permitted locations, excluding admins
                $attendance_query->whereIn('u.location_id', $permitted_locations)
                                 ->whereNotIn('u.id', $admin_user_ids);
            } else {
                $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
            }
        } else {
            // Other roles: only own attendance
            $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
        }

        if ($start_date && $end_date) {
            $attendance_query->whereBetween(DB::raw('DATE(clock_in_time)'), [$start_date, $end_date]);
        }

        // Additional permission safeguard for non-admin users without crud_all_attendance permission
        $can_crud_all_attendance = auth()->user()->can('essentials.crud_all_attendance');
        $can_view_own_attendance = auth()->user()->can('essentials.view_own_attendance');
        
        // For normal users (without crud_all_attendance), only allow viewing their own attendance
        if (!$can_crud_all_attendance && $can_view_own_attendance) {
            // Force employee_id to current user's ID for normal users
            $employee_id = $currentUser->id;
            $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
        } elseif ($employee_id && $can_crud_all_attendance) {
            // Only allow employee_id filter if user has crud_all_attendance permission
            $attendance_query->where('essentials_attendances.user_id', $employee_id);
        }

        $attendances = $attendance_query->groupBy('essentials_attendances.id')->get();

        foreach ($attendances as $attendance) {
            $clock_in = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : null;
            $clock_out = $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : null;
            
            $duration = null;
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $start = \Carbon\Carbon::parse($attendance->clock_in_time);
                $end = \Carbon\Carbon::parse($attendance->clock_out_time);
                $duration = $start->diff($end)->format('%H:%I');
            }

            $event_title = $attendance->employee_name;
            if ($clock_in) {
                $event_title .= " ({$clock_in}" . ($clock_out ? " - {$clock_out}" : "") . ")";
            }

            $events[] = [
                'title' => $event_title,
                'start' => \Carbon\Carbon::parse($attendance->clock_in_time)->format('Y-m-d'),
                'className' => 'present',
                'extendedProps' => [
                    'type' => 'attendance',
                    'employee_name' => $attendance->employee_name,
                    'clock_in' => $clock_in,
                    'clock_out' => $clock_out,
                    'duration' => $duration,
                    'shift_name' => $attendance->shift_name,
                    'ip_address' => $attendance->ip_address,
                    'attendance_id' => $attendance->id
                ]
            ];
        }

        // Get holidays from system (you can customize this based on your holiday table)
        if ($start_date && $end_date) {
            $holidays = $this->getHolidaysForDateRange($business_id, $start_date, $end_date);
            foreach ($holidays as $holiday) {
                $events[] = [
                    'title' => $holiday['name'] . ' (Holiday)',
                    'start' => $holiday['date'],
                    'className' => 'holiday',
                    'extendedProps' => [
                        'type' => 'holiday',
                        'employee_name' => 'All Employees',
                        'holiday_name' => $holiday['name']
                    ]
                ];
            }
        }

        // Get leave data
        if ($start_date && $end_date) {
            $leaves = $this->getLeavesForDateRange($business_id, $start_date, $end_date, $employee_id);

            // Debug: Log leave data returned from DB (will be empty if none found)
            \Log::info('Leave data found: ' . count($leaves));
            \Log::info('Leaves: ' . json_encode($leaves));

            foreach ($leaves as $leave) {
                // Determine leave class based on status
                $leaveClass = 'leave';
                if ($leave['status'] == 'pending') {
                    $leaveClass = 'leave-pending';
                } elseif ($leave['status'] == 'rejected') {
                    $leaveClass = 'leave-rejected';
                }

                // Create title with leave type and status
                $title = $leave['employee_name'] . ' (' . ucfirst($leave['leave_type']) . ' Leave)';
                
                $events[] = [
                    'title' => $title,
                    'start' => $leave['date'],
                    'className' => $leaveClass,
                    'extendedProps' => [
                        'type' => 'leave',
                        'employee_name' => $leave['employee_name'],
                        'leave_type' => $leave['leave_type'],
                        'status' => $leave['status'],
                        'is_paid' => $leave['is_paid'],
                        'leave_days' => $leave['leave_days'],
                        'reason' => $leave['reason'],
                        'max_leave_count' => $leave['max_leave_count']
                    ]
                ];
            }
        }

        // Get holidays (if you have holidays table)
        // You can add holiday logic here if needed

        // Get leave data (if you have leave management)
        // You can add leave logic here if needed

        // If no specific employee selected and user can see all attendance, show absent days
        // Only show absent/sunday days for date ranges within reasonable limits to avoid performance issues  
        if (!$employee_id && $start_date && $end_date && $can_crud_all_attendance) {
            $start_carbon = \Carbon\Carbon::parse($start_date);
            $end_carbon = \Carbon\Carbon::parse($end_date);
            
            // Only calculate absent days for reasonable date ranges (max 60 days)
            if ($start_carbon->diffInDays($end_carbon) <= 60) {
                $users_query = User::where('business_id', $business_id)
                    ->where('is_active', 1);

                // Apply same role-based filtering for absent/sunday days
                if ($user_role_id == 1) {
                    // Admin: can see all users
                } elseif ($user_role_id == 14) {
                    // Sub-admin: limit by permitted locations
                    if (!empty($permitted_locations)) {
                        $users_query->whereIn('location_id', $permitted_locations);
                    } else {
                        $users_query->where('id', $currentUser->id);
                    }
                } else {
                    // Other roles: only themselves
                    $users_query->where('id', $currentUser->id);
                }

                $all_users = $users_query->get();

            $start_carbon = \Carbon\Carbon::parse($start_date);
            $end_carbon = \Carbon\Carbon::parse($end_date);
            
            foreach ($all_users as $user) {
                for ($date = $start_carbon->copy(); $date->lte($end_carbon); $date->addDay()) {
                    $date_str = $date->format('Y-m-d');
                    
                    // Check if user has attendance on this date
                    $has_attendance = $attendances->where('user_id', $user->id)
                        ->filter(function($attendance) use ($date_str) {
                            return \Carbon\Carbon::parse($attendance->clock_in_time)->format('Y-m-d') == $date_str;
                        })->count() > 0;

                    // Check if it's Sunday
                    if ($date->isSunday()) {
                        $full_name = trim($user->surname . ' ' . $user->first_name . ' ' . $user->last_name);
                        $events[] = [
                            'title' => $full_name . ' (Sunday)',
                            'start' => $date_str,
                            'className' => 'sunday',
                            'extendedProps' => [
                                'type' => 'sunday',
                                'employee_name' => $full_name,
                            ]
                        ];
                    } elseif (!$has_attendance && $date->isWeekday()) {
                        // Mark as absent for weekdays without attendance
                        $full_name = trim($user->surname . ' ' . $user->first_name . ' ' . $user->last_name);
                        $events[] = [
                            'title' => $full_name . ' (Absent)',
                            'start' => $date_str,
                            'className' => 'absent',
                            'extendedProps' => [
                                'type' => 'absent',
                                'employee_name' => $full_name,
                            ]
                        ];
                    }
                }
            }
            }
        }

        return response()->json($events);
        
        } catch (\Exception $e) {
            \Log::error('Calendar data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load calendar data'], 500);
        }
    }

    /**
     * Get holidays for date range
     */
    private function getHolidaysForDateRange($business_id, $start_date, $end_date)
    {
        $holidays = [];
        
        // Check if holiday table exists and get holidays
        try {
            $holiday_data = DB::table('essentials_holidays')
                ->where('business_id', $business_id)
                ->whereBetween('start_date', [$start_date, $end_date])
                ->get();
                
            foreach ($holiday_data as $holiday) {
                $start = \Carbon\Carbon::parse($holiday->start_date);
                $end = \Carbon\Carbon::parse($holiday->end_date ?? $holiday->start_date);
                
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $holidays[] = [
                        'name' => $holiday->name,
                        'date' => $date->format('Y-m-d')
                    ];
                }
            }
        } catch (\Exception $e) {
            // If holiday table doesn't exist, add some default holidays
            $holidays[] = ['name' => 'New Year', 'date' => '2025-01-01'];
            $holidays[] = ['name' => 'Independence Day', 'date' => '2025-08-15'];
            $holidays[] = ['name' => 'Gandhi Jayanti', 'date' => '2025-10-02'];
            $holidays[] = ['name' => 'Christmas', 'date' => '2025-12-25'];
        }
        
        return $holidays;
    }

    /**
     * Get month summary statistics for attendance calendar
     *
     * @param  Request  $request
     * @return Response
     */
    public function getMonthSummary(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');

            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $start_date = $request->get('start');
            $end_date = $request->get('end');
            $employee_id = $request->get('employee_id');

            if (!$start_date || !$end_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start and end dates are required'
                ]);
            }

            $currentUser = auth()->user();
            $user_role_id = $currentUser->roleId ?? 0;
            
            // Compute permitted locations for sub-admin style permissions
            $permitted_locations = [];
            if ($user_role_id == 14) {
                $user_permissions = $currentUser->permissions->pluck('name')->all();
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
            }

            // Initialize summary
            $summary = [
                'total_present' => 0,
                'total_absent' => 0,
                'total_half_day' => 0,
                'total_leave' => 0,
                'pl_leave' => 0,
                'cl_leave' => 0,
                'sick_leave' => 0,
                'other_leave' => 0
            ];

            // Get attendance data
            $attendance_query = EssentialsAttendance::where('essentials_attendances.business_id', $business_id)
                ->join('users as u', 'u.id', '=', 'essentials_attendances.user_id')
                ->whereBetween(DB::raw('DATE(clock_in_time)'), [$start_date, $end_date])
                ->select([
                    DB::raw('DATE(clock_in_time) as attendance_date'),
                    'essentials_attendances.user_id',
                    'essentials_attendances.clock_in_time',
                    'essentials_attendances.clock_out_time',
                    'u.location_id'
                ]);

            // Apply role-based filtering
            if ($user_role_id == 1) {
                // Admin: no extra restriction
            } elseif ($user_role_id == 14) {
                if (!empty($permitted_locations)) {
                    $attendance_query->whereIn('u.location_id', $permitted_locations);
                } else {
                    $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
                }
            } else {
                $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
            }

            // Additional permission safeguard for non-admin users without crud_all_attendance permission
            $can_crud_all_attendance = auth()->user()->can('essentials.crud_all_attendance');
            $can_view_own_attendance = auth()->user()->can('essentials.view_own_attendance');
            
            // For normal users (without crud_all_attendance), only allow viewing their own attendance
            if (!$can_crud_all_attendance && $can_view_own_attendance) {
                // Force employee_id to current user's ID for normal users
                $employee_id = $currentUser->id;
                $attendance_query->where('essentials_attendances.user_id', $currentUser->id);
            } elseif ($employee_id && $can_crud_all_attendance) {
                // Only allow employee_id filter if user has crud_all_attendance permission
                $attendance_query->where('essentials_attendances.user_id', $employee_id);
            }

            $attendances = $attendance_query->get();
            
            // Count present days (unique dates)
            $present_dates = $attendances->pluck('attendance_date')->unique();
            $summary['total_present'] = $present_dates->count();

            // Check for half days (attendance with clock_in but no clock_out on same day)
            $half_days = $attendances->whereNull('clock_out_time')->pluck('attendance_date')->unique();
            $summary['total_half_day'] = $half_days->count();

            // Get leave data
            $leave_query = DB::table('essentials_leaves as el')
                ->join('users as u', 'u.id', '=', 'el.user_id')
                ->leftJoin('essentials_leave_types as elt', 'elt.id', '=', 'el.essentials_leave_type_id')
                ->where('el.business_id', $business_id)
                ->where('el.status', 'approved')
                ->whereNotNull('el.start_date')
                ->whereNotNull('el.end_date')
                ->select([
                    'el.start_date',
                    'el.end_date',
                    DB::raw("COALESCE(elt.leave_type, 'General') as leave_type"),
                    'el.user_id',
                    'u.location_id'
                ]);

            // Apply role-based filtering for leaves
            if ($user_role_id == 1) {
                // Admin: no extra restriction
            } elseif ($user_role_id == 14) {
                if (!empty($permitted_locations)) {
                    $leave_query->whereIn('u.location_id', $permitted_locations);
                } else {
                    $leave_query->where('el.user_id', $currentUser->id);
                }
            } else {
                $leave_query->where('el.user_id', $currentUser->id);
            }

            // Apply employee_id filter only if user has permission
            if ($employee_id && $can_crud_all_attendance) {
                $leave_query->where('el.user_id', $employee_id);
            } elseif (!$can_crud_all_attendance && $can_view_own_attendance) {
                // For normal users, force to their own ID
                $leave_query->where('el.user_id', $currentUser->id);
            }

            // Add date range filter
            $leave_query->where(function($query) use ($start_date, $end_date) {
                $query->whereBetween('el.start_date', [$start_date, $end_date])
                      ->orWhereBetween('el.end_date', [$start_date, $end_date])
                      ->orWhere(function($q) use ($start_date, $end_date) {
                          $q->where('el.start_date', '<=', $start_date)
                            ->where('el.end_date', '>=', $end_date);
                      });
            });

            $leaves = $leave_query->get();

            // Count leave days by type
            foreach ($leaves as $leave) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                $range_start = \Carbon\Carbon::parse($start_date);
                $range_end = \Carbon\Carbon::parse($end_date);
                
                // Get overlapping days
                $actual_start = $start->gt($range_start) ? $start : $range_start;
                $actual_end = $end->lt($range_end) ? $end : $range_end;
                
                $leave_days = $actual_start->diffInDays($actual_end) + 1;
                
                $leave_type = strtolower($leave->leave_type);
                
                if (strpos($leave_type, 'pl') !== false || strpos($leave_type, 'privilege') !== false || strpos($leave_type, 'paid') !== false) {
                    $summary['pl_leave'] += $leave_days;
                } elseif (strpos($leave_type, 'cl') !== false || strpos($leave_type, 'casual') !== false) {
                    $summary['cl_leave'] += $leave_days;
                } elseif (strpos($leave_type, 'sick') !== false || strpos($leave_type, 'medical') !== false) {
                    $summary['sick_leave'] += $leave_days;
                } else {
                    $summary['other_leave'] += $leave_days;
                }
                
                $summary['total_leave'] += $leave_days;
            }

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'month' => \Carbon\Carbon::parse($start_date)->format('F Y')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting month summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error calculating summary: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get leaves for date range
     */
    private function getLeavesForDateRange($business_id, $start_date, $end_date, $employee_id = null)
    {
        $leaves = [];
        
        try {
            // First check if tables exist
            \Log::info('Checking for leave data with business_id: ' . $business_id);
            
            $leave_query = DB::table('essentials_leaves as el')
                ->join('users as u', 'u.id', '=', 'el.user_id')
                ->leftJoin('essentials_leave_types as elt', 'elt.id', '=', 'el.essentials_leave_type_id')
                ->where('el.business_id', $business_id)
                ->whereNotNull('el.start_date')
                ->whereNotNull('el.end_date')
                ->select([
                    'el.start_date',
                    'el.end_date', 
                    'el.status',
                    'el.leave_days',
                    'el.reason',
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as employee_name"),
                    DB::raw("COALESCE(elt.leave_type, 'General') as leave_type"),
                    DB::raw("COALESCE(elt.is_paid, 0) as is_paid"),
                    DB::raw("COALESCE(elt.max_leave_count, 0) as max_leave_count")
                ]);

            if ($employee_id) {
                $leave_query->where('el.user_id', $employee_id);
            }

            // Add date range filter
            $leave_query->where(function($query) use ($start_date, $end_date) {
                $query->whereBetween('el.start_date', [$start_date, $end_date])
                      ->orWhereBetween('el.end_date', [$start_date, $end_date])
                      ->orWhere(function($q) use ($start_date, $end_date) {
                          $q->where('el.start_date', '<=', $start_date)
                            ->where('el.end_date', '>=', $end_date);
                      });
            });

            $leave_data = $leave_query->get();
            
            \Log::info('Raw leave query result count: ' . $leave_data->count());
            
            foreach ($leave_data as $leave) {
                $start = \Carbon\Carbon::parse($leave->start_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                
                \Log::info('Processing leave: ' . $leave->employee_name . ' from ' . $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d'));
                
                // Generate dates for each day of leave
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $leaves[] = [
                        'employee_name' => trim($leave->employee_name),
                        'date' => $date->format('Y-m-d'),
                        'leave_type' => $leave->leave_type ?? 'General',
                        'status' => $leave->status ?? 'pending',
                        'is_paid' => $leave->is_paid ?? 0,
                        'leave_days' => $leave->leave_days ?? 1,
                        'reason' => $leave->reason ?? '',
                        'max_leave_count' => $leave->max_leave_count ?? 0
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching leave data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Try alternative approach - check if we can query essentials_leaves directly
            try {
                $simple_leaves = DB::table('essentials_leaves')
                    ->where('business_id', $business_id)
                    // respect employee filter when provided
                    ->when($employee_id, function($q) use ($employee_id) {
                        return $q->where('user_id', $employee_id);
                    })
                    ->where(function($q) use ($start_date, $end_date) {
                        $q->whereBetween('start_date', [$start_date, $end_date])
                          ->orWhereBetween('end_date', [$start_date, $end_date])
                          ->orWhere(function($qq) use ($start_date, $end_date) {
                              $qq->where('start_date', '<=', $start_date)
                                 ->where('end_date', '>=', $end_date);
                          });
                    })
                    ->get();

                \Log::info('Simple leave query found: ' . $simple_leaves->count() . ' records');

                foreach ($simple_leaves as $leave) {
                    $start = \Carbon\Carbon::parse($leave->start_date);
                    $end = \Carbon\Carbon::parse($leave->end_date);

                    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                        $leaves[] = [
                            'employee_name' => 'Employee ID: ' . $leave->user_id,
                            'date' => $date->format('Y-m-d'),
                            'leave_type' => 'Leave',
                            'status' => $leave->status ?? 'pending',
                            'is_paid' => 1,
                            'leave_days' => $leave->leave_days ?? 1,
                            'reason' => $leave->reason ?? '',
                            'max_leave_count' => 0
                        ];
                    }
                }
            } catch (\Exception $e2) {
                \Log::error('Simple leave query also failed: ' . $e2->getMessage());
            }
        }
        
        // Do not inject test/sample data here. Return only DB-sourced leaves (may be empty).
        \Log::info('Final leave data found: ' . count($leaves));
        return $leaves;
    }
}

<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsUserSalesTarget;
use Yajra\DataTables\Facades\DataTables;
  use Carbon\Carbon;
 
class SalesTargetController extends Controller
{
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ModuleUtil  $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! auth()->user()->can('essentials.access_sales_target')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $users = User::where('business_id', $business_id)
                        ->user()
                        ->where('allow_login', 1)
                        ->select(['id',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), ]);

            return Datatables::of($users)
                ->addColumn(
                    'action',
                    '<button type="button" data-href="{{action(\'\Modules\Essentials\Http\Controllers\SalesTargetController@setSalesTarget\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container="#set_sales_target_modal"><i class="fas fa-bullseye"></i>Set Performance Report Points</button>
                    <button type="button" data-href="{{action(\'\Modules\Essentials\Http\Controllers\SalesTargetController@getSalesTarget\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container="#get_sales_target_modal"><i class="fas fa-bullseye"></i>Get Performance Report Points</button>'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"])
                        ->orWhere('username', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('essentials::sales_targets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
     public function setSalesTarget($id)
    {
        $business_id = request()->session()->get('user.business_id');
       
        $user = User::where('business_id', $business_id) 
                    ->find($id); 
  $sales_targets = DB::table('users')
    ->join('performance_parameters', 'performance_parameters.department_id', '=', 'users.essentials_department_id')
    ->select('performance_parameters.id','performance_parameters.parameter_name', 'users.first_name', 'users.last_name')
    ->where('users.id', $id)
    ->get();
    
    
        return view('essentials::sales_targets.sales_target_modal')->with(compact('user', 'sales_targets'));
    }
    
   

public function getSalesTarget(Request $request, $id)
{
    $business_id = request()->session()->get('user.business_id');

    $user = User::where('business_id', $business_id)->find($id); 

    // Get selected month or current month
    $month = $request->input('month', Carbon::now()->format('Y-m'));

    $sales_targets = DB::table('users')
        ->join('performance_parameters', 'performance_parameters.department_id', '=', 'users.essentials_department_id')
        ->leftJoin('essentials_user_sales_targets', function ($join) use ($id, $month) {
            $join->on('performance_parameters.id', '=', 'essentials_user_sales_targets.parameter_id')
                 ->where('essentials_user_sales_targets.user_id', '=', $id)
                 ->whereMonth('essentials_user_sales_targets.created_at', '=', Carbon::parse($month)->month)
                 ->whereYear('essentials_user_sales_targets.created_at', '=', Carbon::parse($month)->year);
        })
        ->select(
            'performance_parameters.id',
            'performance_parameters.parameter_name',
            'users.first_name',
            'users.last_name',
            'essentials_user_sales_targets.self_review',
            'essentials_user_sales_targets.admin_review',
            'essentials_user_sales_targets.created_at'
        )
        ->where('users.id', $id)
        ->get();

    return view('essentials::sales_targets.get_sales_target_modal')->with(compact('user', 'sales_targets', 'month'));
}
public function ajaxTargets(Request $request)
{
    $user_id = $request->user_id;
    $month = $request->month ?? now()->format('Y-m');

    $sales_targets = DB::table('users')
        ->join('performance_parameters', 'performance_parameters.department_id', '=', 'users.essentials_department_id')
        ->leftJoin('essentials_user_sales_targets', function ($join) use ($user_id, $month) {
            $join->on('performance_parameters.id', '=', 'essentials_user_sales_targets.parameter_id')
                ->where('essentials_user_sales_targets.user_id', '=', $user_id)
                ->whereMonth('essentials_user_sales_targets.created_at', '=', \Carbon\Carbon::parse($month)->month)
                ->whereYear('essentials_user_sales_targets.created_at', '=', \Carbon\Carbon::parse($month)->year);
        })
        ->select(
            'performance_parameters.parameter_name',
            'essentials_user_sales_targets.self_review',
            'essentials_user_sales_targets.admin_review',
            'essentials_user_sales_targets.created_at'
        )
        ->where('users.id', $user_id)
        ->get();

    $html = '';
    foreach ($sales_targets as $target) {
        $html .= '<tr>
                    <td>' . $target->parameter_name . '</td>
                    <td>' . ($target->admin_review ?? '-') . '</td>
                    <td>' . ($target->self_review ?? '-') . '</td>
                    <td>' . \Carbon\Carbon::parse($target->created_at)->format('F Y') . '</td>
                 </tr>';
    }

    return response()->json($html);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
 

public function saveSalesTarget(Request $request)
{
    $business_id = request()->session()->get('user.business_id');

    if (
        !(auth()->user()->can('superadmin') ||
        $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) &&
        !auth()->user()->can('essentials.access_sales_target')
    ) {
        abort(403, 'Unauthorized action.');
    }

    try {
        foreach ($request->edit_target as $target) {
            DB::insert("
                INSERT INTO essentials_user_sales_targets 
                    (user_id, self_review, admin_review, parameter_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->user_id,
                $target['self_review'],
                $target['admin_review'],
                $target['parameter_id']
            ]);
        }

        $output = [
            'success' => true,
            'msg' => __('lang_v1.success'),
        ];
    } catch (\Exception $e) {
        \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ];
    }

    return back()->with('status', $output);
}


}

<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsPolicy;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class EssentialsPolicyController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where('business_id', $business_id)->pluck('first_name', 'id');
        $policy_types = EssentialsPolicy::$policy_types;

        if (request()->ajax()) {
            $policies = EssentialsPolicy::where('essentials_policies.business_id', $business_id)
                        ->join('users as u', 'u.id', '=', 'essentials_policies.user_id')
                        ->select([
                            'essentials_policies.id',
                            'essentials_policies.policy_type',
                            'essentials_policies.title',
                            'essentials_policies.status',
                            'essentials_policies.signed_date',
                            DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as user"),
                            'essentials_policies.created_at',
                        ]);

            if (! empty(request()->input('user_id'))) {
                $policies->where('essentials_policies.user_id', request()->input('user_id'));
            }

            if (! empty(request()->input('policy_type'))) {
                $policies->where('essentials_policies.policy_type', request()->input('policy_type'));
            }

            return Datatables::of($policies)
                ->editColumn('policy_type', function($row) {
                    return EssentialsPolicy::$policy_types[$row->policy_type] ?? $row->policy_type;
                })
                ->editColumn('status', function($row) {
                    $status_class = [
                        'pending' => 'bg-yellow',
                        'signed' => 'bg-green',
                        'rejected' => 'bg-red',
                    ];
                    $class = $status_class[$row->status] ?? 'bg-gray';
                    return '<span class="label '.$class.'">'.EssentialsPolicy::$statuses[$row->status].'</span>';
                })
                ->addColumn('action', function($row) {
                    $actions = '<button data-href="'.action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@show', [$row->id]).'" class="btn btn-xs btn-info btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-eye-open"></i> View</button> ';
                    $actions .= '<button data-href="'.action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@edit', [$row->id]).'" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> Edit</button> ';
                    $actions .= '<a href="'.action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@downloadPdf', [$row->id]).'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-download"></i> PDF</a> ';
                    $actions .= '<button data-href="'.action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@destroy', [$row->id]).'" class="btn btn-xs btn-danger delete-row"><i class="glyphicon glyphicon-trash"></i> Delete</button>';
                    return $actions;
                })
                ->rawColumns(['status', 'action'])
                ->make(false);
        }

        return view('essentials::policy.index', compact('users', 'policy_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $users = User::where('business_id', $business_id)->pluck('first_name', 'id');
        $policy_types = EssentialsPolicy::$policy_types;

        return view('essentials::policy.create', compact('users', 'policy_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['user_id', 'policy_type', 'title', 'content', 'status']);
            $input['business_id'] = $business_id;

            // Handle signature photo upload
            if ($request->hasFile('signature_photo')) {
                $file = $request->file('signature_photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/policy_signatures'), $filename);
                $input['signature_photo'] = $filename;
            }

            if ($input['status'] == 'signed') {
                $input['signed_date'] = now()->format('Y-m-d');
            }

            $policy = EssentialsPolicy::create($input);

            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.added_succesfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $policy = EssentialsPolicy::where('business_id', $business_id)->find($id);

        if (! $policy) {
            abort(404, 'Policy not found');
        }

        return view('essentials::policy.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        $policy = EssentialsPolicy::where('business_id', $business_id)->find($id);

        if (! $policy) {
            abort(404, 'Policy not found');
        }

        $users = User::where('business_id', $business_id)->pluck('first_name', 'id');
        $policy_types = EssentialsPolicy::$policy_types;

        return view('essentials::policy.edit', compact('policy', 'users', 'policy_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $policy = EssentialsPolicy::where('business_id', $business_id)->find($id);

            if (! $policy) {
                abort(404, 'Policy not found');
            }

            $input = $request->only(['user_id', 'policy_type', 'title', 'content', 'status', 'rejection_reason']);

            // Handle signature photo upload
            if ($request->hasFile('signature_photo')) {
                // Delete old signature if exists
                if ($policy->signature_photo && file_exists(public_path('uploads/policy_signatures/' . $policy->signature_photo))) {
                    unlink(public_path('uploads/policy_signatures/' . $policy->signature_photo));
                }

                $file = $request->file('signature_photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/policy_signatures'), $filename);
                $input['signature_photo'] = $filename;
            }

            if ($input['status'] == 'signed' && ! $policy->signed_date) {
                $input['signed_date'] = now()->format('Y-m-d');
            }

            $policy->update($input);

            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.crud_policy')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $policy = EssentialsPolicy::where('business_id', $business_id)->find($id);

            if (! $policy) {
                abort(404, 'Policy not found');
            }

            // Delete signature photo if exists
            if ($policy->signature_photo && file_exists(public_path('uploads/policy_signatures/' . $policy->signature_photo))) {
                unlink(public_path('uploads/policy_signatures/' . $policy->signature_photo));
            }

            $policy->delete();

            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.deleted_succesfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Download policy as PDF
     */
    public function downloadPdf($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $policy = EssentialsPolicy::where('business_id', $business_id)->find($id);

        if (! $policy) {
            abort(404, 'Policy not found');
        }

        $pdf = PDF::loadView('essentials::policy.pdf', compact('policy'));
        return $pdf->download('policy_' . $policy->id . '.pdf');
    }
}

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

        $users = User::where('business_id', $business_id)
                    ->select(DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'id')
                    ->pluck('full_name', 'id');

        return view('essentials::policy.index', compact('users'));
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

    /**
     * Get policy template
     */
    public function getTemplate(Request $request)
    {
        $policy_type = $request->input('policy_type');
        
        if (!$policy_type) {
            return response()->json(['success' => false, 'message' => 'Policy type required']);
        }

        $template = \Modules\Essentials\Entities\PolicyTemplates::getTemplate($policy_type);

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Download static policy PDF
     */
    public function downloadPolicyPdf($policy_type)
    {
        $business_id = request()->session()->get('user.business_id');

        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $policy_types = [
            'company_policy' => 'Company Policy',
            'hr_policy' => 'HR Policy',
            'leave_policy' => 'Leave Policy',
            'posh_policy' => 'POSH Policy',
            'nda_policy' => 'NDA Policy',
        ];

        $title = $policy_types[$policy_type] ?? 'Policy';
        $content = \Modules\Essentials\Entities\PolicyTemplates::getTemplate($policy_type);

        $pdf = PDF::loadView('essentials::policy.static_pdf', compact('title', 'content', 'policy_type'));
        return $pdf->download(strtolower(str_replace(' ', '_', $title)) . '.pdf');
    }

    /**
     * Get policy content for user
     */
    public function getPolicyContent(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $user_id = $request->input('user_id');
        $policy_type = $request->input('policy_type');

        if (!$user_id || !$policy_type) {
            return response()->json(['success' => false, 'message' => 'User and policy type required']);
        }

        $content = \Modules\Essentials\Entities\PolicyTemplates::getTemplate($policy_type);

        // Check if signature exists
        $policy = EssentialsPolicy::where('business_id', $business_id)
                                  ->where('user_id', $user_id)
                                  ->where('policy_type', $policy_type)
                                  ->first();

        $signature = null;
        if ($policy && $policy->signature_photo) {
            $signature = asset('uploads/policy_signatures/' . $policy->signature_photo);
        }

        return response()->json([
            'success' => true,
            'content' => $content,
            'signature' => $signature
        ]);
    }

    /**
     * Save user signature
     */
    public function saveSignature(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->input('user_id');
        $policy_type = $request->input('policy_type');

        if (!$user_id || !$policy_type) {
            return response()->json(['success' => false, 'message' => 'User and policy type required']);
        }

        try {
            // Find or create policy
            $policy = EssentialsPolicy::firstOrNew([
                'business_id' => $business_id,
                'user_id' => $user_id,
                'policy_type' => $policy_type
            ]);

            $policy_types = [
                'company_policy' => 'Company Policy',
                'hr_policy' => 'HR Policy',
                'leave_policy' => 'Leave Policy',
                'posh_policy' => 'POSH Policy',
                'nda_policy' => 'NDA Policy',
            ];

            $policy->title = $policy_types[$policy_type];
            $policy->content = \Modules\Essentials\Entities\PolicyTemplates::getTemplate($policy_type);
            $policy->status = 'signed';
            $policy->signed_date = now()->format('Y-m-d');

            // Handle signature upload
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($policy->signature_photo && file_exists(public_path('uploads/policy_signatures/' . $policy->signature_photo))) {
                    unlink(public_path('uploads/policy_signatures/' . $policy->signature_photo));
                }

                $file = $request->file('signature');
                $filename = time() . '_' . $user_id . '_' . $policy_type . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/policy_signatures'), $filename);
                $policy->signature_photo = $filename;
            }

            $policy->save();

            return response()->json([
                'success' => true,
                'message' => 'Signature saved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving signature'
            ]);
        }
    }

    /**
     * Download user-specific policy PDF
     */
    public function downloadUserPolicyPdf(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = $request->input('user_id');
            $policy_type = $request->input('policy_type');

            if (!$user_id || !$policy_type) {
                abort(400, 'User and policy type required');
            }

            $user = User::find($user_id);
            if (!$user) {
                abort(404, 'User not found');
            }

            // Get or create policy
            $policy = EssentialsPolicy::where('business_id', $business_id)
                                      ->where('user_id', $user_id)
                                      ->where('policy_type', $policy_type)
                                      ->first();

            if (!$policy) {
                // Create temporary policy object for PDF
                $policy = new EssentialsPolicy();
                $policy->user_id = $user_id;
                $policy->policy_type = $policy_type;
                $policy->business_id = $business_id;
                
                $policy_types = [
                    'company_policy' => 'Company Policy',
                    'hr_policy' => 'HR Policy',
                    'leave_policy' => 'Leave Policy',
                    'posh_policy' => 'POSH Policy',
                    'nda_policy' => 'NDA Policy',
                ];
                
                $policy->title = $policy_types[$policy_type];
                $policy->content = \Modules\Essentials\Entities\PolicyTemplates::getTemplate($policy_type);
                $policy->status = 'pending';
                $policy->created_at = now();
            }

            // Explicitly set the user relationship
            $policy->setRelation('user', $user);

            $pdf = PDF::loadView('essentials::policy.pdf', compact('policy'));
            $pdf->setOption('isPhpEnabled', false);
            $pdf->setOption('isRemoteEnabled', false);
            return $pdf->download('policy_' . $user->first_name . '_' . $policy_type . '.pdf');
        } catch (\Exception $e) {
            \Log::emergency('PDF Generation Error - File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user signature for policy
     */
    public function getUserSignature(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = $request->input('user_id');
            $policy_type = $request->input('policy_type');

            if (!$user_id || !$policy_type) {
                return response()->json(['success' => false, 'message' => 'User and policy type required']);
            }

            // Get user
            $user = User::find($user_id);
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found']);
            }

            // Check if user has signature in users table
            if ($user->signature_photo) {
                $signaturePath = public_path('uploads/user_signatures/' . $user->signature_photo);
                
                if (file_exists($signaturePath)) {
                    return response()->json([
                        'success' => true,
                        'signature' => '/I_DO/public/uploads/user_signatures/' . $user->signature_photo,
                        'signed_date' => now()->format('d-m-Y'),
                        'user_name' => $user->first_name . ' ' . $user->last_name
                    ]);
                }
            }

            // Fallback: Check if signature exists in policies table
            $policy = EssentialsPolicy::where('business_id', $business_id)
                                      ->where('user_id', $user_id)
                                      ->where('policy_type', $policy_type)
                                      ->first();

            if ($policy && $policy->signature_photo) {
                $signaturePath = public_path('uploads/policy_signatures/' . $policy->signature_photo);
                
                if (file_exists($signaturePath)) {
                    return response()->json([
                        'success' => true,
                        'signature' => asset('uploads/policy_signatures/' . $policy->signature_photo),
                        'signed_date' => $policy->signed_date ? \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') : now()->format('d-m-Y'),
                        'user_name' => $user->first_name . ' ' . $user->last_name
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'No signature found'
            ]);
        } catch (\Exception $e) {
            \Log::emergency('Get Signature Error - File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading signature: ' . $e->getMessage()
            ], 500);
        }
    }
}

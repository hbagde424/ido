<?php

namespace Modules\Essentials\Http\Controllers;

use App\BusinessLocation;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsMessageGroup;
use Modules\Essentials\Entities\EssentialsMessageGroupMember;

class EssentialsMessageGroupController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
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
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.view_message') && ! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        
        // Get groups where user is a member
        $groups = EssentialsMessageGroup::where('business_id', $business_id)
            ->whereHas('members', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with(['members', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('essentials::message_groups.index')
                ->with(compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        // Get all users in the business (excluding current user)
        $users = User::where('business_id', $business_id)
                    ->where('id', '!=', auth()->user()->id)
                    ->select('id', 'first_name', 'last_name', 'username')
                    ->get();

        return view('essentials::message_groups.create')
                ->with(compact('users'));
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
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = $request->session()->get('user.id');

            $input = $request->only(['group_name', 'group_description', 'members']);
            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;

            $group = EssentialsMessageGroup::create($input);

            // Add members to the group
            if (!empty($input['members'])) {
                $members = is_array($input['members']) ? $input['members'] : [$input['members']];
                
                // Add creator as member
                $members[] = $user_id;
                $members = array_unique($members);

                foreach ($members as $member_id) {
                    $group->addMember($member_id, $user_id);
                }
            } else {
                // Add only creator as member
                $group->addMember($user_id, $user_id);
            }

            $output = [
                'success' => true,
                'msg' => __('essentials::lang.group_created_successfully'),
                'group_id' => $group->id
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.view_message') && ! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        
        $group = EssentialsMessageGroup::where('business_id', $business_id)
            ->whereHas('members', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with(['members', 'creator', 'messages.sender'])
            ->findOrFail($id);

        return view('essentials::message_groups.show')
                ->with(compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        
        $group = EssentialsMessageGroup::where('business_id', $business_id)
            ->where('created_by', $user_id) // Only creator can edit
            ->with(['members'])
            ->findOrFail($id);

        // Get all users in the business (excluding current user)
        $users = User::where('business_id', $business_id)
                    ->where('id', '!=', $user_id)
                    ->select('id', 'first_name', 'last_name', 'username')
                    ->get();

        return view('essentials::message_groups.edit')
                ->with(compact('group', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = $request->session()->get('user.id');

            $group = EssentialsMessageGroup::where('business_id', $business_id)
                ->where('created_by', $user_id) // Only creator can update
                ->findOrFail($id);

            $input = $request->only(['group_name', 'group_description', 'members']);
            $group->update($input);

            // Update members
            if (isset($input['members'])) {
                // Remove all existing members except creator
                $group->members()->wherePivot('user_id', '!=', $user_id)->detach();
                
                // Add new members
                if (!empty($input['members'])) {
                    $members = is_array($input['members']) ? $input['members'] : [$input['members']];
                    
                    // Add creator as member if not already
                    if (!in_array($user_id, $members)) {
                        $members[] = $user_id;
                    }
                    $members = array_unique($members);

                    foreach ($members as $member_id) {
                        if ($member_id != $user_id) { // Don't re-add creator
                            $group->addMember($member_id, $user_id);
                        }
                    }
                }
            }

            $output = [
                'success' => true,
                'msg' => __('essentials::lang.group_updated_successfully')
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = auth()->user()->id;

            $group = EssentialsMessageGroup::where('business_id', $business_id)
                ->where('created_by', $user_id) // Only creator can delete
                ->findOrFail($id);

            $group->delete();

            $output = [
                'success' => true,
                'msg' => __('essentials::lang.group_deleted_successfully')
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Get users for group creation/editing.
     *
     * @return Response
     */
    public function getUsers()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where('business_id', $business_id)
                    ->where('id', '!=', auth()->user()->id)
                    ->select('id', 'first_name', 'last_name', 'username')
                    ->get();

        return response()->json($users);
    }
}

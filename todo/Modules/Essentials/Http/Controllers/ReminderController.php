<?php

namespace Modules\Essentials\Http\Controllers;

use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\Reminder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\GlobalFunction;

class ReminderController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $moduleUtil;

    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
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

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        $auth_id = auth()->user()->id;
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => $user_id,
                'business_id' => $business_id,
            ];

            $events = Reminder::getReminders($data);

            return $events;
        }

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
             if (! $is_admin) {
                 
                 $find_permission_id = DB::table('model_has_permissions')
                 ->where('model_id', '=', $auth_id)
                ->select('permission_id')
                 
                ->get()->pluck('permission_id');
              
                // Query the database
                $users = DB::table('model_has_roles')
                    ->leftJoin('model_has_permissions', 'model_has_permissions.model_id', '=', 'model_has_roles.model_id')
                    ->leftJoin('users', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
                    ->where('role_id', '!=', 1)
                    // Optional: Exclude specific model_id if needed
                    // ->where('model_has_roles.model_id', '!=', $auth_id)
                    ->whereIn('permissions.id', $find_permission_id)
                    ->select('users.first_name', 'users.last_name', 'users.id')
                    ->get();
                
                // Capitalize first_name and last_name, concatenate them, then filter out blank names
                $users = $users->map(function ($user) {
                    $firstName = ucwords(strtolower($user->first_name));
                    $lastName = ucwords(strtolower($user->last_name));
                    $fullName = trim($firstName . ' ' . $lastName);
                    return [
                        'full_name' => $fullName,
                        'id' => $user->id
                    ];
                })->filter(function ($user) {
                    return !empty($user['full_name']);
                })->pluck('full_name', 'id');
                
             }else{
                $users = User::forDropdown($business_id, false, false, false, true);
             }
            
        }
        
        
        return view('essentials::reminder.index')->with(compact('users'));;
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

        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');

                $input = $request->only(['name', 'date', 'repeat','users', 'time',
                    'end_time', ]);

                $reminder['date'] = $this->commonUtil->uf_date($input['date']);
                $reminder['time'] = $this->commonUtil->uf_time($input['time']);
                $reminder['end_time'] = ! empty($input['end_time']) ? $this->commonUtil->uf_time($input['end_time']) : null;
                $reminder['name'] = $input['name'];
                $reminder['added_by'] =$user_id ;
                $reminder['repeat'] = $input['repeat'];
                $reminder['user_id'] =  $input['users'];
                $reminder['business_id'] = $business_id;

                Reminder::create($reminder);
                
                
                 $user = User::find($input['users']);
                     if(!empty($user->fcmtoken)){
                    $firebasedata = GlobalFunction::sendPushNotificationToUser('New Reminder Added',$user->fcmtoken, '0');
                    }
                    
                    

                $output = [
                    'userid'=>$input['users'],
                    'users'=>$user,
                    'firebasenew12'=>$firebasedata,
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];

                return $output;
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];

                return back()->with('status', $output);
            }
        }
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

        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $reminder = Reminder::where('business_id', $business_id)
                              ->where('user_id', $user_id)
                              ->find($id);

            $time = $this->commonUtil->format_time($reminder->time);

            $repeat = [
                'one_time' => __('essentials::lang.one_time'),
                'every_day' => __('essentials::lang.every_day'),
                'every_week' => __('essentials::lang.every_week'),
                'every_month' => __('essentials::lang.every_month'),
            ];

            return view('essentials::reminder.show')
                ->with(compact('reminder', 'time', 'repeat'));
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
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = request()->session()->get('user.id');

                $repeat = $request->only('repeat');

                Reminder::where('business_id', $business_id)
                    ->where('user_id', $user_id)
                    ->where('id', $id)
                    ->update($repeat);

                $output = ['success' => true,
                    'msg' => trans('lang_v1.updated_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = ['success' => 0,
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
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $user_id = request()->session()->get('user.id');

                Reminder::where('business_id', $business_id)
                  ->where('user_id', $user_id)
                  ->where('id', $id)
                  ->delete();

                $output = ['success' => true,
                    'msg' => trans('lang_v1.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                $output = ['success' => 0,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}

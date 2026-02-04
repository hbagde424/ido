<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User; 
use DB;
use App\Media;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Essentials\Entities\EssentialsTodoComment;
use Modules\Essentials\Entities\ToDo;
use Modules\Essentials\Entities\Reminder;
use Modules\Essentials\Notifications\NewTaskCommentNotification;
use Modules\Essentials\Notifications\NewTaskDocumentNotification;
use Modules\Essentials\Notifications\NewTaskNotification;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\GlobalFunction;
use App\Notifications\FcmNotification;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Essentials\Entities\EssentialsUserShift;

class AuthController extends Controller
{
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

public function checkIn(Request $request)
{
    //Log::info($request->all());
    $validated = $request->validate([
        'user_id' => 'required|integer',
        'business_id' => 'required|integer',
        'shift_id' => 'required',
        'ip_address' => 'nullable|string',
        'clock_in_location' => 'nullable|string',
        'clock_in_note' => 'nullable|string'
    ]);

    // Check if the user already has an open attendance
   $existing = DB::table('essentials_attendances')
            ->where('user_id', $validated['user_id'])
            //->whereNull('clock_out_time')
            ->whereDate('clock_in_time', Carbon::today())
            ->first();

    if ($existing) {
        return response()->json([
            'status' => false,
            'message' => 'User already clocked in.'
        ], 400);
    }

    // Insert attendance
    DB::table('essentials_attendances')->insert([
        'user_id' => $validated['user_id'],
        'business_id' => $validated['business_id'],
        'essentials_shift_id' => $validated['shift_id'],
        'ip_address' => $validated['ip_address'] ?? null,
        'clock_in_time' => now(),
        'clock_in_location' => $validated['clock_in_location'] ?? null,
        'clock_in_note' => $validated['clock_in_note'] ?? null,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Clocked in successfully.'
    ]);
}

public function checkOut(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|integer',
        'clock_out_location' => 'nullable|string',
        'clock_out_note' => 'nullable|string'
    ]);

    $attendance = DB::table('essentials_attendances')
        ->where('user_id', $validated['user_id'])
        ->whereNull('clock_out_time')
        ->orderByDesc('id')
        ->first();

    if (!$attendance) {
        return response()->json([
            'status' => false,
            'message' => 'No active attendance found.'
        ], 404);
    }

    DB::table('essentials_attendances')
        ->where('id', $attendance->id)
        ->update([
            'clock_out_time' => now(),
            'clock_out_location' => $validated['clock_out_location'] ?? null,
            'clock_out_note' => $validated['clock_out_note'] ?? null,
            'updated_at' => now()
        ]);

    return response()->json([
        'status' => true,
        'message' => 'Clocked out successfully.'
    ]);
}

    public function deleteTodo(Request $request)
    {
        $id = $request->id;
        $toDoItem = ToDo::find($id);

        // Check if the item exists
        if ($toDoItem) {
            // Delete the item
            $toDoItem->delete();

            // Optionally, you can return a response
            return response()->json(['status' => true, 'message' => 'To-do item deleted successfully'], 200);
        } else {
            // Return an error response if the item is not found
            return response()->json(['status' => false, 'message' => 'To-do item not found'], 404);
        }
    }

    public function deleteReminder(Request $request)
    {
        $id = $request->id;
        $toDoItem = Reminder::find($id);

        // Check if the item exists
        if ($toDoItem) {
            // Delete the item
            $toDoItem->delete();

            // Optionally, you can return a response
            return response()->json(['status' => true, 'message' => 'Reminder deleted successfully'], 200);
        } else {
            // Return an error response if the item is not found
            return response()->json(['status' => false, 'message' => 'Reminder not found'], 404);
        }
    }

   public function login(Request $request)
{
    // Manually validate the request
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|string',
        'fcmtoken' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    // Find the user by email or username
    $user = User::where(function ($query) use ($request) {
        $query->where('email', $request->input('email'))
              ->orWhere('username', $request->input('email'));
    })->first();

    // Check password
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['status' => false, 'message' => 'Wrong Id & Password Plz Check Again'], 200);
    }

    // Update FCM token
    $user->fcmtoken = $request->fcmtoken;
    $user->save();

    // Get user's role name
    $role_name = $this->moduleUtil->getUserRoleName($user->id);

    // Get user's current shift if any
    $shift = EssentialsUserShift::where('user_id', $user->id)
        ->whereNull('end_date') // Active shift
        ->first();

    $user->essentials_shift_id = $shift ? $shift->essentials_shift_id : 0;

    // ✅ Get all permissions (from role or directly)
    $all_permissions = $user->getAllPermissions()->pluck('name')->toArray();

    // ✅ Filter and extract location-based permissions like 'location.11'
    $location_permissions = array_filter($all_permissions, function ($perm) {
        return str_starts_with($perm, 'location.');
    });

    // ✅ Extract only location IDs (e.g., 11 from location.11)
    $location_ids = array_map(function ($perm) {
        return explode('.', $perm)[1] ?? null;
    }, $location_permissions);

    // ✅ Clean nulls and duplicates
    $location_ids = array_filter(array_unique($location_ids));

    // ✅ Create comma-separated location string
    $locations_comma_separated = implode(',', $location_ids);

    return response()->json([
        'status' => true,
        'message' => 'login',
        'role' => $role_name,
        'shift' => $shift,
        'data' => $user,
        // 'permissions' => $all_permissions,
        'locations' => $locations_comma_separated
    ], 200);
}


    public function markTodosAsRead(Request $request)
{
    $request->validate([
        'todo_id' => 'required|integer',
    ]);

    // Update is_read to 0 for all todos assigned to the given user
    $updated = DB::table('essentials_to_dos')
        ->where('id', $request->todo_id)
        ->update(['is_read' => 0]);

    return response()->json([
        'status' => true,
        'message' => "Marked to-dos as Read " 
    ], 200);
}

public function todolist(Request $request)
{
    $request->validate([
        'userid' => 'required'
    ]);

    // Fetch assigned to-dos (without users join)
   $data = DB::table('essentials_to_dos')
    ->join('essentials_todos_users', 'essentials_to_dos.id', '=', 'essentials_todos_users.todo_id')
    ->select('essentials_to_dos.*')
    ->where('essentials_todos_users.user_id', $request->userid)
    ->orderBy('essentials_to_dos.id', 'desc') // <- Order by latest
    ->get();


    // Collect task IDs
    $taskIds = $data->pluck('id');

    // Get comment counts per task
    $commentCounts = DB::table('essentials_todo_comments')
        ->select('task_id', DB::raw('COUNT(*) as comment_count'))
        ->whereIn('task_id', $taskIds)
        ->groupBy('task_id')
        ->pluck('comment_count', 'task_id');

    // Add first_name, last_name, and task_color
    foreach ($data as $todo) {
        $user = DB::table('users')->where('id', $todo->created_by)->first();
        $todo->first_name = $user->first_name ?? null;
        $todo->last_name = $user->last_name ?? null;

        $commentCount = $commentCounts[$todo->id] ?? 0;
        $todo->task_color = $commentCount > 0 ? 'red' : 'green';
    }

    // Get task counts grouped by status
    $taskCounts = DB::table('essentials_to_dos')
        ->join('essentials_todos_users', 'essentials_to_dos.id', '=', 'essentials_todos_users.todo_id')
        ->where('essentials_todos_users.user_id', $request->userid)
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

    // Ensure all statuses are present with default 0
    $statuses = ['In progress', 'Incomplete', 'Completed'];
    $taskSummary = array_fill_keys($statuses, 0);

    foreach ($taskCounts as $status => $count) {
        $taskSummary[$status] = $count;
    }

    // Calculate total number of tasks assigned to the user
    $totalTasks = array_sum($taskSummary);

    // Calculate progress percentage (if there are tasks)
    $progressPercentage = 0;
    if ($totalTasks > 0) {
        $completedTasks = $taskSummary['Completed'] ?? 0;
        $progressPercentage = ($completedTasks / $totalTasks) * 100;
    }

    // Determine progress description
    $progressDescription = '';
    $progressColour = 'red';
    if ($progressPercentage >= 80) {
        $progressDescription = 'Excellent';
        $progressColour ='green';
    } elseif ($progressPercentage >= 70) {
        $progressDescription = 'Average';
        $progressColour='orange';
    } elseif ($progressPercentage >= 10) {
        $progressDescription = 'Weak';
        $progressColour ='red';
    } else {
        $progressDescription = 'Very Weak';
        $progressColour='red';
    }

    // Check if user is currently checked in
    
 $existing = DB::table('essentials_attendances')
            ->where('user_id',$request->userid)
            ->whereNull('clock_out_time')
            ->whereDate('clock_in_time', Carbon::today())
            ->first();

    
    $checkin = $existing ? true : false;

    return response()->json([
        'checkin' => $checkin,
        'status' => true,
        'message' => 'To-Do list',
        'data' => $data,
        'task_counts' => $taskSummary,
        'progress_percentage' => $progressPercentage,
        'progress_description' => $progressDescription,
       'progress_colour' =>$progressColour
    ], 200);
}



    public function todocommentlist(Request $request)
    {
        $request->validate([
            'task_id' => 'required',
        ]);

        $data = DB::table('essentials_to_dos')
            ->join('essentials_todo_comments', 'essentials_to_dos.id', '=', 'essentials_todo_comments.task_id')
            ->join('users', 'essentials_todo_comments.comment_by', '=', 'users.id')

            ->select('essentials_todo_comments.*', 'users.first_name', 'users.last_name')
            ->where('essentials_todo_comments.task_id', $request->task_id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'To Do Commment list',
            'data' => $data,

        ], 200);
    }

    public function addTodoComment(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:essentials_to_dos,id',
            'comment' => 'required|string|max:255',
            'userid' => 'required',
        ]);

        $commentData = [
            'task_id' => $request->task_id,
            'comment' => $request->comment,
            'comment_by' => $request->userid,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $commentId = DB::table('essentials_todo_comments')->insertGetId($commentData);
        
         DB::table('essentials_to_dos')
        ->where('id', $request->task_id)
        ->update(['is_read' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Comment added successfully',
            'comment_id' => $commentId,
        ], 200);
    }

    public function reminderlist(Request $request)
    {
        
      
    
        $request->validate([
            'userid' => 'required',
        ]);


        $data = DB::table('essentials_reminders')
            ->join('users', 'essentials_reminders.user_id', '=', 'users.id')
            ->select('essentials_reminders.*', 'users.first_name', 'users.last_name')
            ->where('users.id', $request->userid)
            ->orderBy('essentials_reminders.id', 'desc')
            ->get();

        if ($request->userid == 1) {
            $data = DB::table('essentials_reminders')
                ->join('users', 'essentials_reminders.user_id', '=', 'users.id')
                ->select('essentials_reminders.*', 'users.first_name', 'users.last_name')
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'Reminder List',
            'data' => $data,

        ], 200);
    }

    public function remindercreate(Request $request)
    {

        $request->validate([
            'userid' => 'required',
            'name' => 'required',
            'time' => 'required',
            'date' => 'required',
            'end_time' => 'required',
            'repeat' => 'required'
        ]);


        $created_by = $request->userid;
        $input = $request->only(
            'userid',
            'name',
            'date',
            'assign_to',
            'time',
            'end_time',
            'repeat'
        );

        $input['added_by'] = $created_by;
        $input['user_id'] = $created_by;

        if ($input['role'] != 'user') {
            $input['added_by'] = $input['userid'];
            $input['user_id'] = $input['assign_to'];
            $created_by = $input['assign_to'];
        }


        $input['name'] = $input['name'];
        $input['date'] = $input['date'];
        $input['end_date'] = $input['end_date'];
        $input['end_time'] = $input['end_time'];
        $input['business_id'] = 1;

        $input['status'] = !empty($input['status']) ? $input['status'] : 'new';
        $users = $request->userid;
        $to_dos = Reminder::create($input);

        $this->commonUtil->activityLog($to_dos, 'added');


        $user = User::find($created_by);
        $deviceToken = $user->fcmtoken;

        if (!empty($deviceToken)) {
            $firebasedata = GlobalFunction::sendPushNotificationToUser('New Reminder Added', $deviceToken, '0');
        }

        // \Notification::send($users, new NewTaskNotification($to_dos));
        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'Reminder create',
            'data' => $input,
            'firebaseres' => $firebasedata

        ], 200);
    }

 
public function todocreate(Request $request)
{
    //  dd($request->all());
    \Log::info('Assign To Value: ', $request->all());

    $request->validate([
        // 'userid' => 'required', // Single user ID (creator)
        'task' => 'required',
        'description' => 'required',
        'estimated_hours' => 'required',
        // 'priority' => 'required',
        // 'status' => 'required',
        // 'role' => 'required', // Role is required
        // 'assign_to' => 'required' // We will manually convert it into an array
    ]);

    $created_by = $request->userid; // Creator ID
    $assign_to = $request->assign_to;
    Log::info('Assign To Value: ' . $assign_to);

    if (is_string($assign_to)) {
    $assign_to = trim($assign_to);

    if (str_starts_with($assign_to, '[') && str_ends_with($assign_to, ']')) {
        $assign_to = json_decode($assign_to, true);
    } else {
        $assign_to = explode(',', str_replace(['[', ']'], '', $assign_to));
    }
    }
    
    $assignedUsers = array_map('intval', $assign_to);
 

    $input = $request->only(
        'task',
        'date',
        'description',
        'estimated_hours',
        'priority',
        'status',
        'end_date',
        'role',
        'project_checklist_id'
    );

    // Set business ID
    $input['business_id'] = 1;
    $input['estimated_hours'] = 0;
$input['status'] = !empty($input['status']) 
    ? ($input['status'] === 'new' ? 'Incomplete' : $input['status']) 
    : 'Incomplete';

    // Get current date if not provided or invalid
   $currentDateTime = now()->toDateTimeString(); // Get current date and time in 'Y-m-d H:i:s' format

// Check if 'date' is empty or less than current date and time
if (empty($input['date']) || $input['date'] < $currentDateTime) {
    $input['date'] = $currentDateTime; // Set to current date and time
}else {
    // Add the current time to the date in 'Y-m-d' format
    $input['date'] = $input['date'] . ' ' . date('H:i:s'); // Use 24-hour format for time
}
    $todoList = [];
    foreach ($assignedUsers as $userId) {
        // Default behavior
        $input['created_by'] = $created_by;
        $input['user_id'] = $userId;

        // Apply role-based logic
        if ($input['role'] != 'user') {
            $input['created_by'] = $created_by;
            $input['user_id'] = $userId;
        }

        $toDo = ToDo::create($input);
        $toDo->users()->sync([$userId]); // Assign the ToDo to the user

        // Send notifications
        $user = User::find($userId);
        if ($user) {
            $deviceToken = $user->fcmtoken;
            if (!empty($deviceToken)) {
                if($created_by!=$userId){
                GlobalFunction::sendPushNotificationToUser('New Todo Added', $deviceToken, '0');
                }
            }
            \Notification::send($user, new NewTaskNotification($toDo));
        } else {
            \Log::error("User with ID {$userId} not found, cannot send notification.");
        }

        $this->commonUtil->activityLog($toDo, 'added');
        $todoList[] = $toDo;
    }

    return response()->json([
        'status' => true,
        'message' => 'To do created for assigned users',
        'data' => $todoList
    ], 200);
}

public function sendTodoNotifiction(Request $request)
{
    $request->validate([
        'userid' => 'required',
        'todo_id' => 'required'
    ]);

    $userId = $request->userid; // Target user ID
    $todo = ToDo::find($request->todo_id);

    if (!$todo) {
        return response()->json([
            'status' => false,
            'message' => 'Todo item not found.'
        ], 404);
    }

    $title = $todo->task;
    $createdBy = $todo->created_by ?? null;

    $user = User::find($userId);
    if ($user) {
        $deviceToken = $user->fcmtoken;

        if (!empty($deviceToken)) {
            if ($createdBy != $userId) {
                GlobalFunction::sendPushNotificationToUser("Your '$title' task is pending", $deviceToken, '0');
            }
        }
    } else {
        \Log::error("User with ID {$userId} not found, cannot send notification.");
    }

    return response()->json([
        'status' => true,
        'message' => 'Notification sent if applicable',
    ], 200);
}
 
    public function todoupdate(Request $request)
    {

        if (!$request->has('only_status')) {
            $input = $request->only(
                'task',
                'date',
                'description',
                'estimated_hours',
                'priority',
                'status',
                'end_date'
            );

            $input['date'] = $this->commonUtil->uf_date($input['date'], true);
            $input['end_date'] = !empty($input['end_date']) ? $this->commonUtil->uf_date($input['end_date'], true) : null;

            $input['status'] = !empty($input['status']) ? $input['status'] : 'new';
        } else {
            $input = ['status' => !empty($request->input('status')) ? $request->input('status') : null];
        }

        $query = ToDo::where('business_id', $business_id);

        //Non admin can update only assigned tasks
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            $query->where(function ($query) {
                $query->where('created_by', auth()->user()->id)
                    ->orWhereHas('users', function ($q) {
                        $q->where('user_id', auth()->user()->id);
                    });
            });
        }

        $todo = $query->findOrFail($id);

        $todo_before = $todo->replicate();

        $todo->update($input);

        if (auth()->user()->can('essentials.assign_todos') && !$request->has('only_status')) {
            $users = $request->input('users');
            $todo->users()->sync($users);
        }

        $this->commonUtil->activityLog($todo, 'edited', $todo_before);
    }

    public function getCustomers(Request $request)
    {
        $request->validate([
            'userid' => 'required',
        ]);
    
        $auth_id = $request->input('userid');
        $user = User::where('id', $auth_id)->first();
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
    
        $business_id = 1;
        $is_admin = $this->moduleUtil->is_admin($user, $business_id);
 
        $users = [];
    
        if ($is_admin) {
            $find_permission_id = DB::table('model_has_permissions')
                ->where('model_id', '=', $auth_id)
                ->pluck('permission_id');
    // dd($find_permission_id);
            $users = DB::table('model_has_roles1111')
                ->leftJoin('model_has_permissions', 'model_has_permissions.model_id', '=', 'model_has_roles.model_id')
                ->leftJoin('users', 'users.id', '=', 'model_has_roles.model_id')
                ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
                ->where('role_id', '!=', 1)
                ->whereIn('permissions.id', $find_permission_id)
                ->select('users.first_name', 'users.last_name', 'users.id')
                ->get();
// dd($users);
            $users = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => ucwords(strtolower($user->first_name)),
                    'last_name' => ucwords(strtolower($user->last_name)),
                ];
            })->filter(function ($user) {
                return !empty($user['first_name']) && !empty($user['last_name']);
            });
        } else {
            // dd($user);
            $users = User::forDropdown($business_id, false)->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => ucwords(strtolower($user->first_name)),
                    'last_name' => ucwords(strtolower($user->last_name)),
                ];
            });
        }
    
    
    
    
        return response()->json([
            'checkin'=>$checkin,
            'status' => true,
            'message' => 'User List',
            'data' => $users->values(), // Ensure the data is returned as an array of objects
        ], 200);
    }


  public function userstore(Request $request)
    {
        // Validate Request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:191|unique:users,username',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'roleId' => 'required|integer',  // Role ID should be an integer
            'location_id' => 'required|integer', // Location ID should be an integer
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        try {
            // Create User
            $user = User::create([
                'user_type' => 'user', // Default user type
                'roleId' => $request->roleId, // Assign role ID
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'language' => 'en', // Default language
                'location_id' => $request->location_id,
                'allow_login' => 1, // Default allow login
                'status' => 'active', // Default status
            ]);

            return response()->json([
                'success' => true,
                'msg' => __('user.user_added'),
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }
    

public function getAllUsers()
{
    try {
        $users = User::get();
        return response()->json([
            'success' => true,
            'users' => $users
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching users: '.$e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ], 500);
    }
}

public function getAllUsersWithCount(Request $request)
{
    try {
        $users = User::select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.username',
                'users.email',
                'users.location_id',
                'users.roleId',
                DB::raw("COALESCE(categories.name, 'NA') as designation")
            )
            ->leftJoin('categories', function($join) {
                $join->on('users.essentials_designation_id', '=', 'categories.id')
                     ->where('categories.category_type', '=', 'hrm_designation');
            })
            ->with([
                'tasks' => function ($query) {
                    $query->select('id', 'user_id', 'status')
                          ->selectRaw('COUNT(*) OVER(PARTITION BY user_id, status) as count');
                }
            ])
            ->get();

        $users->transform(function ($user) {
            $taskCounts = $user->tasks->pluck('count', 'status');

            $taskIds = $user->tasks->pluck('id');

            $hasCommentedTask = DB::table('essentials_todo_comments')
                ->whereIn('task_id', $taskIds)
                ->exists();

            $taskColor = $hasCommentedTask ? 'red' : 'green';

            $hasUnread = DB::table('essentials_to_dos')
                ->where('essentials_to_dos.user_id', $user->id)
                ->where('essentials_to_dos.is_read', 1)
                ->exists();

            return array_merge($user->toArray(), [
                'task_color' => $taskColor,
                'isunreadtodo' => $hasUnread ? 1 : 0
            ]);
        });



 $existing = DB::table('essentials_attendances')
            ->where('user_id',$request->userid)
            ->whereNull('clock_out_time')
            ->whereDate('clock_in_time', Carbon::today())
            ->first();
 
    
    $checkin = $existing ? true : false; 

        return response()->json([
            'checkin'=>$checkin,
            'success' => true,
            'users' => $users
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching users: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ], 500);
    }
}



public function getRolesAndLocations()
{
    try {
        // Fetch Roles
        $roles = DB::table('roles')
            ->select(['id', DB::raw("REGEXP_REPLACE(name, '[^A-Za-z ]', '') as name")]) // Removes numbers and special characters
            ->get();

        // Fetch Business Locations
        $locations = DB::table('business_locations')->select(['id', 'name'])->where('is_active',1)->get();

        return response()->json([
            'success' => true,
            'roles' => $roles,
            'locations' => $locations
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching roles and locations: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ], 500);
    }
}


 
public function updateTaskStatus(Request $request)
{
    // Validate request
    $request->validate([
        'todo_id' => 'required',
        'status' => 'required',
    ]);

    // Find the To-Do item
    $todo = ToDo::find($request->todo_id);

    if (!$todo) {
        return response()->json([
            'status' => false,
            'message' => 'To-Do not found',
        ], 404);
    }

    // Update the status
    $todo->status = $request->status;
    if($request->status=='Completed')
    {
        $currentDateTime = now()->toDateTimeString();
         $todo->end_date =$currentDateTime;
    }
    
    $todo->save();

    // Log activity
    $this->commonUtil->activityLog($todo, 'status updated'); 
    
    return response()->json([
        'status' => true,
        'message' => 'To-Do status updated successfully'
    ], 200);
}
  
public function updateUser(Request $request)
{
    // Validate Request Data
     $id = $request->id;
     
    
    $validator = Validator::make($request->all(), [
         'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
         'email' => 'sometimes|email',
        'roleId' => 'sometimes|integer',
        'location_id' => 'sometimes|integer'
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
    }

    try {
        // Find User
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'msg' => 'User not found'], 404);
        }

        // Update User Fields
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
         if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        // if ($request->has('email')) {
        //     $user->email = $request->email;
        // }
        // if ($request->has('username')) {
        //     $user->username = $request->username;
        // }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('roleId')) {
            $user->roleId = $request->roleId;
        }
        if ($request->has('location_id')) {
            $user->location_id = $request->location_id;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'msg' => 'User updated successfully',
            'user' => $user
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error updating user: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ], 500);
    }
}


public function deleteUser($id)
{
    try {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'msg' => 'User not found'
            ], 404);
        }

        $user->delete(); // Soft delete if SoftDeletes is enabled, otherwise hard delete.

        return response()->json([
            'success' => true,
            'msg' => 'User deleted successfully'
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error deleting user: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
        ], 500);
    }
}


 public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_locations,name',
        ]);

        try {
            $id = DB::table('business_locations')->insertGetId([
                'name' => $request->name,
                'business_id'=>1,
                'invoice_scheme_id'=>1,
                'invoice_layout_id'=>1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Location added successfully',
                'location_id' => $id
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    // ✅ 3. Update Location
    public function update(Request $request)
    {
            $id = $request->id;
            
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

    


        try {
            $updated = DB::table('business_locations')->where('id', $id)->update([
                'name' => $request->name,
                'updated_at' => now()
            ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Location not found or no changes made',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Location updated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    // ✅ 4. Delete Location
    public function delete($id)
    {
        try {
            $deleted = DB::table('business_locations')->where('id', $id)->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Location not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Location deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting location: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ], 500);
        }
    }
    
    

}

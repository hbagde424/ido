<?php

namespace Modules\Essentials\Http\Controllers;

use App\BusinessLocation;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsMessage;
use Modules\Essentials\Entities\EssentialsMessageGroup;
use Modules\Essentials\Entities\EssentialsMessageRecipient;
use Modules\Essentials\Notifications\NewMessageNotification;

class EssentialsMessageController extends Controller
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
        $message_type = request()->get('type', 'location'); // location, user, group

        $query = EssentialsMessage::where('business_id', $business_id)
                        ->with(['sender', 'recipient', 'group', 'location']);

        // Filter based on message type
        if ($message_type == 'location') {
            $query->locationMessages();
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->where(function ($q) use ($permitted_locations) {
                    $q->whereIn('location_id', $permitted_locations)
                        ->orWhereRaw('location_id IS NULL');
                });
            }
        } elseif ($message_type == 'user') {
            $query->userMessages()
                  ->where(function ($q) use ($user_id) {
                      $q->where('recipient_user_id', $user_id)
                        ->orWhere('user_id', $user_id);
                  });
        } elseif ($message_type == 'group') {
            $query->groupMessages()
                  ->whereHas('group.members', function ($q) use ($user_id) {
                      $q->where('user_id', $user_id);
                  });
        }

        $messages = $query->orderBy('created_at', 'ASC')->get();

        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // Get users for user messaging
        $users = User::where('business_id', $business_id)
                    ->where('id', '!=', $user_id)
                    ->select('id', 'first_name', 'last_name', 'username')
                    ->get();

        // Get groups for group messaging
        $groups = EssentialsMessageGroup::where('business_id', $business_id)
            ->whereHas('members', function($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })
            ->with(['members'])
            ->get();

        return view('essentials::messages.index')
                ->with(compact('messages', 'business_locations', 'users', 'groups', 'message_type'));
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

        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');

                $input = $request->only(['message', 'location_id', 'message_type', 'recipient_user_id', 'group_id']);
                $input['business_id'] = $business_id;
                $input['user_id'] = $user_id;
                $input['message'] = nl2br($input['message']);

                // Set default message type if not provided
                if (empty($input['message_type'])) {
                    $input['message_type'] = 'location';
                }

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];

                if (! empty($input['message'])) {
                    // Validate message type specific requirements
                    if ($input['message_type'] == 'user' && empty($input['recipient_user_id'])) {
                        $output = [
                            'success' => false,
                            'msg' => __('essentials::lang.please_select_recipient')
                        ];
                        return $output;
                    }

                    if ($input['message_type'] == 'group' && empty($input['group_id'])) {
                        $output = [
                            'success' => false,
                            'msg' => __('essentials::lang.please_select_group')
                        ];
                        return $output;
                    }

                    // Get last message for notification throttling
                    $last_message = $this->__getLastMessage($input);

                    $message = EssentialsMessage::create($input);

                    // Create message recipients for tracking
                    $this->__createMessageRecipients($message, $input);

                    // Check if min 10min passed from last message for notification throttling
                    $database_notification = empty($last_message) || $last_message->created_at->diffInMinutes(\Carbon::now()) > 10;
                    $this->__notify($message, $database_notification);

                    $output['html'] = view('essentials::messages.message_div', compact('message'))->render();
                }
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
        $business_id = request()->user()->business_id;
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = request()->user()->id;

                EssentialsMessage::where('business_id', $business_id)
                            ->where('user_id', $user_id)
                            ->where('id', $id)
                            ->delete();

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
     * Get last message for notification throttling.
     *
     * @param array $input
     * @return EssentialsMessage|null
     */
    private function __getLastMessage($input)
    {
        $query = EssentialsMessage::where('business_id', $input['business_id'])
                                 ->where('user_id', $input['user_id']);

        if ($input['message_type'] == 'location') {
            $query->where('location_id', $input['location_id'])
                  ->orWhereNull('location_id');
        } elseif ($input['message_type'] == 'user') {
            $query->where('recipient_user_id', $input['recipient_user_id']);
        } elseif ($input['message_type'] == 'group') {
            $query->where('group_id', $input['group_id']);
        }

        return $query->orderBy('created_at', 'desc')->first();
    }

    /**
     * Create message recipients for tracking.
     *
     * @param EssentialsMessage $message
     * @param array $input
     * @return void
     */
    private function __createMessageRecipients($message, $input)
    {
        $business_id = $input['business_id'];
        $sender_id = $input['user_id'];
        $recipients = [];

        if ($input['message_type'] == 'location') {
            // For location messages, get all users in that location
            $query = User::where('id', '!=', $sender_id)
                        ->where('business_id', $business_id);

            if (empty($input['location_id'])) {
                $users = $query->get();
            } else {
                $users = $query->permission('location.'.$input['location_id'])->get();
            }

            foreach ($users as $user) {
                $recipients[] = [
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        } elseif ($input['message_type'] == 'user') {
            // For user messages, add the recipient
            $recipients[] = [
                'message_id' => $message->id,
                'user_id' => $input['recipient_user_id'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        } elseif ($input['message_type'] == 'group') {
            // For group messages, add all group members except sender
            $group = EssentialsMessageGroup::find($input['group_id']);
            if ($group) {
                foreach ($group->members as $member) {
                    if ($member->id != $sender_id) {
                        $recipients[] = [
                            'message_id' => $message->id,
                            'user_id' => $member->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }

        if (!empty($recipients)) {
            EssentialsMessageRecipient::insert($recipients);
        }
    }

    /**
     * Sends notification to the user.
     *
     * @return void
     */
    private function __notify($message, $database_notification = true)
    {
        $business_id = request()->session()->get('user.business_id');
        $users = collect();

        if ($message->message_type == 'location') {
            $query = User::where('id', '!=', $message->user_id)
                        ->where('business_id', $business_id);

            if (empty($message->location_id)) {
                $users = $query->get();
            } else {
                $users = $query->permission('location.'.$message->location_id)->get();
            }
        } elseif ($message->message_type == 'user') {
            // For user messages, notify only the recipient
            $users = User::where('id', $message->recipient_user_id)->get();
        } elseif ($message->message_type == 'group') {
            // For group messages, notify all group members except sender
            if ($message->group) {
                $users = $message->group->members()->where('user_id', '!=', $message->user_id)->get();
            }
        }

        if ($users->count()) {
            $message->database_notification = $database_notification;
            \Notification::send($users, new NewMessageNotification($message));
        }
    }

    /**
     * Function to get recent messages
     *
     * @return void
     */
    public function getNewMessages()
    {
        $last_chat_time = request()->input('last_chat_time');
        $message_type = request()->input('type', 'location');

        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (! auth()->user()->can('essentials.view_message') && ! auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        $query = EssentialsMessage::where('business_id', $business_id)
                        ->where('user_id', '!=', $user_id)
                        ->with(['sender', 'recipient', 'group', 'location'])
                        ->orderBy('created_at', 'ASC');

        if (! empty($last_chat_time)) {
            $query->where('created_at', '>', $last_chat_time);
        }

        // Filter based on message type
        if ($message_type == 'location') {
            $query->locationMessages();
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->where(function ($q) use ($permitted_locations) {
                    $q->whereIn('location_id', $permitted_locations)
                        ->orWhereRaw('location_id IS NULL');
                });
            }
        } elseif ($message_type == 'user') {
            $query->userMessages()
                  ->where('recipient_user_id', $user_id);
        } elseif ($message_type == 'group') {
            $query->groupMessages()
                  ->whereHas('group.members', function ($q) use ($user_id) {
                      $q->where('user_id', $user_id);
                  });
        }

        $messages = $query->get();

        return view('essentials::messages.recent_messages')->with(compact('messages'));
    }
}

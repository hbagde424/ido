<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Essentials\Entities\Reminder;
use App\User;
use Carbon\Carbon;
use App\Models\GlobalFunction;
use Illuminate\Support\Facades\Log;

class SendReminderNotification extends Command
{
    protected $signature = 'reminders:send-notifications';
    protected $description = 'Send push notifications to users when their reminder time matches the current time.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Cron job started: Checking reminders.');

        // Get the current date and time
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i');

        Log::info("Current date & time: $currentDate $currentTime");

        // Find reminders that match the current date and time
        $reminders = Reminder::where('date', $currentDate)
            ->where('time', $currentTime)
            ->get();

        if ($reminders->isEmpty()) {
            Log::info('No reminders found at this time.');
        }

        foreach ($reminders as $reminder) {
            $user = User::find($reminder->user_id);
            
            if (!empty($user) && !empty($user->fcmtoken)) {
                Log::info("Sending notification to User ID: {$user->id}, Reminder ID: {$reminder->id}");

                // Send push notification
                $firebasedata = GlobalFunction::sendPushNotificationToUser(
                    'Reminder Alert: ' . $reminder->name,
                    $user->fcmtoken,
                    '0'
                );

                Log::info('Push notification response:', ['response' => $firebasedata]);
            } else {
                Log::warning("User ID: {$reminder->user_id} has no FCM token, skipping notification.");
            }
        }

        Log::info('Cron job finished.');
    }
}
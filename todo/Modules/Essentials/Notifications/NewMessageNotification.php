<?php

namespace Modules\Essentials\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = $this->message->database_notification ? ['database'] : [];
        if (isPusherEnabled()) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'from' => $this->message->sender->user_full_name,
            'from_id' => $this->message->user_id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $title = __('essentials::lang.new_message');
        $body = '';
        $link = action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index']);

        if ($this->message->message_type == 'user') {
            $title = __('essentials::lang.new_direct_message');
            $body = strip_tags(__('essentials::lang.new_direct_message_notification', [
                'sender' => $this->message->sender->user_full_name
            ]));
            $link = action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'user']);
        } elseif ($this->message->message_type == 'group') {
            $title = __('essentials::lang.new_group_message');
            $body = strip_tags(__('essentials::lang.new_group_message_notification', [
                'sender' => $this->message->sender->user_full_name,
                'group' => $this->message->group ? $this->message->group->group_name : 'Unknown Group'
            ]));
            $link = action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'group']);
        } else {
            $body = strip_tags(__('essentials::lang.new_message_notification', ['sender' => $this->message->sender->user_full_name]));
        }

        return new BroadcastMessage([
            'title' => $title,
            'body' => $body,
            'link' => $link,
        ]);
    }
}

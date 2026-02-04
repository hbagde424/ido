<?php
namespace App\Channels;

use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class FcmChannel
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function send($notifiable, $notification)
    {
        $deviceToken = $notifiable->device_token;

        if (!$deviceToken) {
            return;
        }

        $message = CloudMessage::withTarget($deviceToken)
            ->withNotification([
                'title' => $notification->title,
                'body' => $notification->body,
            ]);

        $this->messaging->send($message);
    }
}

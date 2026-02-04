<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class FcmNotification extends Notification
{
    private $title;
    private $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        return (object)[
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}

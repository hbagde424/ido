<?php
namespace App\Services;

use Kreait\Firebase\Messaging\Messaging;

class FirebaseService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification($deviceToken, $title, $body)
    {
        $message = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'token' => $deviceToken,
        ];

        return $this->messaging->send($message);
    }
}

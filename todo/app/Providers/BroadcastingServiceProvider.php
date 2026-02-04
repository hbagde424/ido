<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Broadcasting\BroadcastManager;
use App\Broadcasting\FcmBroadcaster; // Replace with your broadcaster class

class BroadcastingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make(BroadcastManager::class)->extend('fcm', function ($app) {
            return new FcmBroadcaster($app['firebase.messaging']);
        });
    }
}

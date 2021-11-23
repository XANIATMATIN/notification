<?php

namespace MatinUtils\Notifications;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('notifications', function ($app) {
            return new Notification;
        });
    }
    
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
    }
    
    public function provides()
    {
        return [];
    }

}

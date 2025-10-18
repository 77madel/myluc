<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Désactive les tentatives d'envoi d'email en dev
        if ($this->app->environment('local')) {
            $this->app->extend('mail.manager', function($manager) {
                $manager->setDefaultDriver('array');
                return $manager;
            });
        }
    }
}

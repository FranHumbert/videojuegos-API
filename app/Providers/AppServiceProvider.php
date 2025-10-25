<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Gate para acceso solo admin
        Gate::define('admin-only', function ($user) {
            return $user->role === 'admin';
        });

        // Define Gate para user o admin
        Gate::define('user-or-admin', function ($user) {
            return in_array($user->role, ['user', 'admin']);
        });
    }
}
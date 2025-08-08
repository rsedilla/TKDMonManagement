<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Define a simple admin gate for now
        // In production, implement proper role-based access control
        Gate::define('admin', function (User $user) {
            // For now, all authenticated users are admins
            // TODO: Implement proper role system with spatie/laravel-permission
            return true;
        });

        // Define resource-specific gates
        Gate::define('view-consolidations', function (User $user) {
            return true; // TODO: Add role check
        });

        Gate::define('edit-consolidations', function (User $user) {
            return true; // TODO: Add role check
        });

        Gate::define('delete-consolidations', function (User $user) {
            return true; // TODO: Add role check
        });

        // Similar gates for other resources
        Gate::define('view-leaders', function (User $user) {
            return true; // TODO: Add role check
        });

        Gate::define('edit-leaders', function (User $user) {
            return true; // TODO: Add role check
        });
    }
}

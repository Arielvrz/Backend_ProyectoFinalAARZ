<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
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
        // Gate global para verificar si es admin
        Gate::define('is-admin', function (User $user) {
            return $user->role->name === 'admin';
        });

        // Gate para bodeguero O admin (puede gestionar stock)
        Gate::define('manage-inventory', function (User $user) {
            return in_array($user->role->name, ['admin', 'bodeguero', 'despacho']);
        });
    }
}

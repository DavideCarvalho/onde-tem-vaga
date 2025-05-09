<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
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
        //

        Gate::define('viewPulse', function (User $user) {
            return $user->email === 'admin@example.com';
        });

        Gate::define('viewWebTinker', function ($user = null) {
            return $user->email === 'admin@example.com';
        });

        Model::automaticallyEagerLoadRelationships();
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        Gate::before(fn (User $user) => $user->isAdmin() ?: null);

        RateLimiter::for('votes', fn ($request) => Limit::perMinute(20)->by($request->user()->id));

        RateLimiter::for('auth', fn ($request) => Limit::perMinute(15)->by($request->ip()));
    }
}

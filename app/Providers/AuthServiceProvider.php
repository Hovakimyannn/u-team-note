<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Gates\NoteGate;
use App\Services\Auth\SsoGuard;
use App\Services\Auth\SsoProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('sso', function (Application $app) {
            return new SsoProvider($app['hash']);
        });

        Auth::extend('sso', function (Application $app, $name) {
            return new SsoGuard(
                $name,
                $this->app->get(SsoProvider::class),
                $this->app['session.store'],
            );
        });

        Gate::define('is_owner', [NoteGate::class, 'isOwner']);
    }
}

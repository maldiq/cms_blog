<?php

namespace App\Providers;

use App\Domain\User\Listeners\UpdateLastLoginOnLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
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
        Event::listen(Login::class, UpdateLastLoginOnLogin::class);

        // Samakan URL generated (Filament menu, redirect login) dengan host yang dipakai browser.
        if (! $this->app->runningInConsole() && $this->app->environment('local')) {
            URL::useOrigin(request()->getSchemeAndHttpHost());
        }
    }
}

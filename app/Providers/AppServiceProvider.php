<?php

namespace App\Providers;

use App\Domain\Setting\Policies\SettingsPolicy;
use App\Domain\User\Listeners\UpdateLastLoginOnLogin;
use App\Domain\User\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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

        Gate::define('manage-settings', fn (User $user): bool => (new SettingsPolicy())->manage($user));

        // Samakan URL generated (Filament menu, redirect login) dengan host yang dipakai browser.
        if (! $this->app->runningInConsole() && $this->app->environment('local')) {
            URL::useOrigin(request()->getSchemeAndHttpHost());
        }
    }
}

<?php

namespace App\Providers;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Post\Observers\PostObserver;
use App\Domain\Comment\Events\CommentCreated;
use App\Domain\Comment\Listeners\SendNewCommentAdminNotification;
use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Menu\Observers\MenuItemObserver;
use App\Domain\Menu\Observers\MenuObserver;
use App\Domain\Page\Models\Page;
use App\Domain\Page\Observers\PageObserver;
use App\Domain\Service\Models\Service;
use App\Domain\Service\Observers\ServiceObserver;
use App\Domain\Setting\Policies\SettingsPolicy;
use App\Domain\Setting\Settings\MailSettings;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Observers\ThemeSettingObserver;
use App\Domain\User\Listeners\UpdateLastLoginOnLogin;
use App\Domain\User\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;

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
        Event::listen(CommentCreated::class, SendNewCommentAdminNotification::class);

        Menu::observe(MenuObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Post::observe(PostObserver::class);
        Page::observe(PageObserver::class);
        Service::observe(ServiceObserver::class);
        ThemeSetting::observe(ThemeSettingObserver::class);

        Gate::define('manage-settings', fn (User $user): bool => (new SettingsPolicy)->manage($user));

        $this->applyMailSettings();

        // Komponen Livewire di App\Domain\* (bukan App\Livewire) perlu resolver agar request /livewire/update tidak 419.
        Livewire::resolveMissingComponent(function (string $name): ?string {
            if (! str_starts_with($name, 'app.domain.')) {
                return null;
            }

            $relative = substr($name, strlen('app.domain.'));
            $class = 'App\\Domain\\'.collect(explode('.', $relative))
                ->map(fn (string $segment): string => Str::studly($segment))
                ->implode('\\');

            if (class_exists($class) && is_subclass_of($class, LivewireComponent::class)) {
                return $class;
            }

            return null;
        });

        // Samakan URL generated (Filament menu, redirect login) dengan host yang dipakai browser.
        if (! $this->app->runningInConsole() && $this->app->environment('local')) {
            URL::useOrigin(request()->getSchemeAndHttpHost());
        }
    }

    /**
     * Pakai konfigurasi SMTP dari halaman Pengaturan agar semua email
     * aplikasi (termasuk contact form dan email antrean) ikut memakainya.
     */
    private function applyMailSettings(): void
    {
        try {
            $mail = $this->app->make(MailSettings::class);
        } catch (\Throwable) {
            // Tabel settings belum ada (mis. saat migrate:fresh) atau grup belum di-seed.
            return;
        }

        if (! in_array($mail->driver, ['smtp', 'sendmail'], true)) {
            return;
        }

        if ($mail->driver === 'smtp') {
            if (blank($mail->host)) {
                return;
            }

            config([
                'mail.mailers.smtp.host' => $mail->host,
                'mail.mailers.smtp.port' => $mail->port ?: 587,
                'mail.mailers.smtp.username' => $mail->username,
                'mail.mailers.smtp.password' => $mail->password,
                'mail.mailers.smtp.scheme' => $mail->encryption === 'ssl' ? 'smtps' : null,
            ]);
        }

        config([
            'mail.default' => $mail->driver,
            'mail.from.address' => $mail->from_address ?: config('mail.from.address'),
            'mail.from.name' => $mail->from_name ?: config('mail.from.name'),
        ]);
    }
}

<?php

namespace App\Providers\Filament;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource;
use App\Domain\Comment\Filament\Resources\CommentResource;
use App\Domain\Gallery\Album\Filament\Resources\AlbumResource;
use App\Domain\Page\Filament\Resources\PageResource;
use App\Domain\Blog\Post\Filament\Resources\PostResource;
use App\Domain\Blog\Series\Filament\Resources\SeriesResource;
use App\Domain\Blog\Tag\Filament\Resources\TagResource;
use App\Domain\Language\Filament\Resources\LanguageResource;
use App\Domain\Menu\Filament\Resources\MenuResource;
use App\Domain\Media\Filament\Resources\MediaResource;
use App\Domain\Role\Filament\Resources\RoleResource;
use App\Domain\User\Filament\Resources\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('kelola')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->resources([
                UserResource::class,
                RoleResource::class,
                LanguageResource::class,
                MediaResource::class,
                MenuResource::class,
                PostResource::class,
                CategoryResource::class,
                TagResource::class,
                SeriesResource::class,
                AlbumResource::class,
                CommentResource::class,
                PageResource::class,
            ])
            ->discoverResources(in: app_path('Domain/Blog'), for: 'App\\Domain\\Blog')
            ->discoverResources(in: app_path('Domain/Gallery'), for: 'App\\Domain\\Gallery')
            ->discoverResources(in: app_path('Domain/Menu/Filament/Resources'), for: 'App\\Domain\\Menu\\Filament\\Resources')
            ->discoverResources(in: app_path('Domain/Media/Filament/Resources'), for: 'App\\Domain\\Media\\Filament\\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Domain/Setting/Filament/Pages'), for: 'App\\Domain\\Setting\\Filament\\Pages')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

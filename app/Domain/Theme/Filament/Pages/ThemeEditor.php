<?php

namespace App\Domain\Theme\Filament\Pages;

use Filament\Pages\Page;

class ThemeEditor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Theme Editor';

    protected static ?string $slug = 'theme-editor';

    protected static string $view = 'filament.pages.theme-editor';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }
}

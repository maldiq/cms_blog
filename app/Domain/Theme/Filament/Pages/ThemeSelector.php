<?php

namespace App\Domain\Theme\Filament\Pages;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Services\ThemeService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ThemeSelector extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Appearance';

    protected static ?string $navigationLabel = 'Themes';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Themes';

    protected static ?string $slug = 'themes';

    protected static string $view = 'filament.pages.theme-selector';

    /**
     * @var list<array<string, mixed>>
     */
    public array $themes = [];

    public string $activeSlug = '';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->refreshThemes();
    }

    public function refresh(): void
    {
        $this->refreshThemes();

        Notification::make()
            ->title('Daftar theme diperbarui')
            ->success()
            ->send();
    }

    public function activateTheme(string $slug): void
    {
        abort_unless(static::canAccess(), 403);

        $manifest = $this->scanThemeManifests()->firstWhere('slug', $slug);

        if ($manifest === null) {
            Notification::make()
                ->title('Theme tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        Theme::query()->update(['is_active' => false]);

        $theme = Theme::query()->updateOrCreate(
            ['slug' => $manifest['slug']],
            [
                'name' => $manifest['name'],
                'description' => $manifest['description'] ?? null,
                'author' => $manifest['author'] ?? null,
                'version' => $manifest['version'] ?? '1.0.0',
                'preview_image' => $manifest['preview'] ?? null,
                'is_active' => true,
            ],
        );

        $theme->update(['is_active' => true]);

        app(ThemeService::class)->clearCache();
        Theme::clearCache();

        $this->activeSlug = $slug;
        $this->refreshThemes();

        Notification::make()
            ->title('Theme diaktifkan')
            ->body($theme->name)
            ->success()
            ->send();

        $this->redirect(ThemeEditor::getUrl(), navigate: true);
    }

    protected function refreshThemes(): void
    {
        $this->syncThemesFromDisk();
        $this->themes = $this->buildThemeCards();
        $this->activeSlug = (string) (Theme::query()->where('is_active', true)->value('slug') ?? '');
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function buildThemeCards(): array
    {
        $activeSlugs = Theme::query()->where('is_active', true)->pluck('slug', 'slug');

        return $this->scanThemeManifests()
            ->map(function (array $manifest) use ($activeSlugs): array {
                $slug = $manifest['slug'];
                $previewFile = $manifest['preview'] ?? null;
                $previewUrl = null;

                if (filled($previewFile)) {
                    $publicPath = public_path('themes/' . $slug . '/' . ltrim((string) $previewFile, '/'));
                    $themePath = resource_path('views/themes/' . $slug . '/' . ltrim((string) $previewFile, '/'));

                    if (is_file($publicPath)) {
                        $previewUrl = asset('themes/' . $slug . '/' . ltrim((string) $previewFile, '/'));
                    } elseif (is_file($themePath)) {
                        $previewUrl = asset('themes/' . $slug . '/' . ltrim((string) $previewFile, '/'));
                    }
                }

                return [
                    'slug' => $slug,
                    'name' => $manifest['name'] ?? $slug,
                    'author' => $manifest['author'] ?? null,
                    'version' => $manifest['version'] ?? '1.0.0',
                    'description' => $manifest['description'] ?? null,
                    'preview_url' => $previewUrl,
                    'is_active' => $activeSlugs->has($slug),
                ];
            })
            ->values()
            ->all();
    }

    protected function syncThemesFromDisk(): void
    {
        foreach ($this->scanThemeManifests() as $manifest) {
            Theme::query()->updateOrCreate(
                ['slug' => $manifest['slug']],
                [
                    'name' => $manifest['name'] ?? $manifest['slug'],
                    'description' => $manifest['description'] ?? null,
                    'author' => $manifest['author'] ?? null,
                    'version' => $manifest['version'] ?? '1.0.0',
                    'preview_image' => $manifest['preview'] ?? null,
                ],
            );
        }
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function scanThemeManifests(): Collection
    {
        $basePath = resource_path('views/themes');

        if (! is_dir($basePath)) {
            return collect();
        }

        $manifests = collect();

        foreach (File::directories($basePath) as $directory) {
            $manifestPath = $directory . DIRECTORY_SEPARATOR . 'theme.json';

            if (! is_file($manifestPath)) {
                continue;
            }

            $contents = json_decode((string) file_get_contents($manifestPath), true);

            if (! is_array($contents)) {
                continue;
            }

            $slug = $contents['slug'] ?? basename($directory);
            $contents['slug'] = $slug;

            $manifests->push($contents);
        }

        return $manifests;
    }
}

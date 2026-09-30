<?php

namespace App\Domain\Theme\Filament\Pages;

use App\Domain\Theme\Filament\Support\ThemeEditorFormSchema;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Services\ThemeService;
use Filament\Actions\Action;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class ThemeEditor extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Theme Editor';

    protected static ?string $slug = 'theme-editor';

    protected static string $view = 'filament.pages.theme-editor';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $theme = Theme::current();

        if ($theme === null) {
            Notification::make()
                ->title('Belum ada theme aktif')
                ->body('Aktifkan theme terlebih dahulu dari halaman Themes.')
                ->warning()
                ->send();

            $this->redirect(ThemeSelector::getUrl(), navigate: true);

            return;
        }

        $this->loadFormState($theme);
    }

    public function form(Form $form): Form
    {
        $schemaBuilder = new ThemeEditorFormSchema;

        return $form
            ->schema([
                Tabs::make('ThemeSettings')
                    ->tabs($schemaBuilder->tabs())
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $theme = Theme::current();

        if ($theme === null) {
            Notification::make()
                ->title('Theme aktif tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        $state = $this->form->getState();

        foreach ($state as $group => $settings) {
            if (! is_string($group) || ! is_array($settings)) {
                continue;
            }

            foreach ($settings as $key => $value) {
                if (! is_string($key)) {
                    continue;
                }

                ThemeSetting::set($theme->id, $group, $key, $value);
            }
        }

        app(ThemeService::class)->clearCache();

        Notification::make()
            ->title('Pengaturan theme disimpan')
            ->success()
            ->send();
    }

    public function resetToDefault(): void
    {
        $theme = Theme::current();

        if ($theme === null) {
            Notification::make()
                ->title('Theme aktif tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        ThemeSetting::query()->where('theme_id', $theme->id)->delete();

        Theme::clearCache();
        app(ThemeService::class)->clearCache();

        \App\Support\Theme\ThemeDefaultSettingsSeeder::runForTheme($theme);

        $this->loadFormState($theme);

        Notification::make()
            ->title('Pengaturan direset ke default')
            ->success()
            ->send();
    }

    protected function loadFormState(Theme $theme): void
    {
        $schemaBuilder = new ThemeEditorFormSchema;
        $data = [];

        foreach (ThemeEditorFormSchema::settingsGroups() as $group) {
            $fromDatabase = ThemeSetting::getGroup($theme->id, $group);
            $data[$group] = $schemaBuilder->mergeGroupDefaults($group, $fromDatabase);
        }

        $this->form->fill($data);
    }

    /**
     * @return array<int, Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
            Action::make('resetToDefault')
                ->label('Reset ke default')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->resetToDefault()),
        ];
    }
}

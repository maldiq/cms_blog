<?php

namespace App\Domain\Theme\Filament\Concerns;

use App\Domain\Language\Models\Language;
use Filament\Forms;
use Filament\Forms\Components\Component;

trait InteractsWithThemeLocaleTabs
{
    /**
     * Tab locale di dalam tab group theme (id, en).
     *
     * @param  callable(string $locale, string $prefix): list<Component>  $fieldsForLocale
     */
    protected function themeLocaleTabs(string $prefix, callable $fieldsForLocale): Forms\Components\Tabs
    {
        $tabs = [];

        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $tabs[] = Forms\Components\Tabs\Tab::make("{$prefix}_{$locale}")
                ->label(strtoupper($locale))
                ->schema($fieldsForLocale($locale, $prefix));
        }

        return Forms\Components\Tabs::make("locale_{$prefix}")
            ->tabs($tabs)
            ->columnSpanFull();
    }

    /**
     * @return array{id: string, en: string}
     */
    protected function emptyTranslatable(): array
    {
        $values = [];

        foreach (Language::getActive() as $language) {
            $values[$language->code] = '';
        }

        return $values;
    }
}

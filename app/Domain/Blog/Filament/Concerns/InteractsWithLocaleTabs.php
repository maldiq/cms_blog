<?php

namespace App\Domain\Blog\Filament\Concerns;

use App\Domain\Language\Models\Language;
use Filament\Forms;
use Filament\Forms\Components\Component;

trait InteractsWithLocaleTabs
{
    /**
     * @param  callable(string): list<Component>  $fieldsForLocale
     */
    protected static function localeTabs(callable $fieldsForLocale): Forms\Components\Tabs
    {
        $tabs = [];

        foreach (Language::getActive() as $language) {
            $tabs[] = Forms\Components\Tabs\Tab::make($language->code)
                ->label(strtoupper($language->code))
                ->schema($fieldsForLocale($language->code));
        }

        return Forms\Components\Tabs::make('translations')->tabs($tabs);
    }
}

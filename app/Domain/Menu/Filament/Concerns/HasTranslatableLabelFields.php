<?php

namespace App\Domain\Menu\Filament\Concerns;

use App\Domain\Language\Models\Language;
use Filament\Forms;
use Filament\Forms\Components\Component;
trait HasTranslatableLabelFields
{
    /**
     * @return list<Component>
     */
    protected static function translatableLabelFields(): array
    {
        $fields = [];

        foreach (Language::getActive() as $language) {
            $code = $language->code;

            $fields[] = Forms\Components\TextInput::make("label_{$code}")
                ->label("Label ({$code})")
                ->required($code === 'id')
                ->maxLength(255)
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, ?\App\Domain\Menu\Models\MenuItem $record) use ($code): void {
                    if ($record === null) {
                        return;
                    }

                    $component->state($record->getTranslation('label', $code, false));
                })
                ->dehydrated(true);
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function mergeLabelTranslations(array $data): array
    {
        $translations = [];

        foreach (Language::getActive() as $language) {
            $key = "label_{$language->code}";

            if (array_key_exists($key, $data)) {
                $translations[$language->code] = $data[$key];
                unset($data[$key]);
            }
        }

        $data['label'] = $translations;

        return $data;
    }
}

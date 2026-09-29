<?php

namespace App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages;

use App\Domain\Blog\Series\Filament\Resources\SeriesResource;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Language\Models\Language;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    protected static string $resource = SeriesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncTranslations($this->record, $this->form->getState());
    }

    protected function syncTranslations(Series $series, array $state): void
    {
        foreach (Language::getActive() as $language) {
            $bucket = $state['translations'][$language->code] ?? null;

            if (is_array($bucket)) {
                $series->translateOrNew($language->code)->fill($bucket)->save();
            }
        }
    }
}

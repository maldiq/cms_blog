<?php

namespace App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages;

use App\Domain\Blog\Series\Filament\Resources\SeriesResource;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeries extends EditRecord
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Series $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'description', 'meta_title', 'meta_description',
                ]);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterSave(): void
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

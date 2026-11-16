<?php

namespace App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages;

use App\Domain\Blog\Series\Filament\Resources\SeriesResource;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Filament\Concerns\InteractsWithMediaImagePicker;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateSeries extends CreateRecord
{
    use InteractsWithMediaImagePicker;

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

            if (! is_array($bucket) || blank($bucket['title'] ?? null)) {
                continue;
            }

            $series->translateOrNew($language->code)->fill(Arr::only($bucket, [
                'title', 'slug', 'description', 'meta_title', 'meta_description',
            ]))->save();
        }
    }
}

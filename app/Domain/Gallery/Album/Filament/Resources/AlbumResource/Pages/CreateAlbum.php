<?php

namespace App\Domain\Gallery\Album\Filament\Resources\AlbumResource\Pages;

use App\Domain\Gallery\Album\Filament\Resources\AlbumResource;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Language\Models\Language;
use Filament\Resources\Pages\CreateRecord;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Album $album */
        $album = $this->record;
        $this->syncTranslations($album, $this->form->getState());
    }

    /**
     * @param  array<string, mixed>  $state
     */
    protected function syncTranslations(Album $album, array $state): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $state['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $album->translateOrNew($locale)->fill($bucket)->save();
        }
    }
}

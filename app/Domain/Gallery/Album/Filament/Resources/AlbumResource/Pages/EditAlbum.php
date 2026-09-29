<?php

namespace App\Domain\Gallery\Album\Filament\Resources\AlbumResource\Pages;

use App\Domain\Gallery\Album\Filament\Resources\AlbumResource;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Album $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'description',
                ]);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterSave(): void
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

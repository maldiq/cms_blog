<?php

namespace App\Domain\Blog\Tag\Filament\Resources\TagResource\Pages;

use App\Domain\Blog\Tag\Filament\Resources\TagResource;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Language\Models\Language;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncTranslations($this->record, $this->form->getState());
    }

    protected function syncTranslations(Tag $tag, array $state): void
    {
        foreach (Language::getActive() as $language) {
            $bucket = $state['translations'][$language->code] ?? null;

            if (is_array($bucket)) {
                $tag->translateOrNew($language->code)->fill($bucket)->save();
            }
        }
    }
}

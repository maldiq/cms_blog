<?php

namespace App\Domain\Blog\Category\Filament\Resources\CategoryResource\Pages;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource;
use App\Domain\Blog\Category\Models\Category;
use App\Domain\Language\Models\Language;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

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
        /** @var Category $category */
        $category = $this->record;
        $state = $this->form->getState();
        $this->syncTranslations($category, $state);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    protected function syncTranslations(Category $category, array $state): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $state['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $category->translateOrNew($locale)->fill($bucket)->save();
        }
    }
}

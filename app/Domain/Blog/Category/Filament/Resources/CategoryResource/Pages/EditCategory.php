<?php

namespace App\Domain\Blog\Category\Filament\Resources\CategoryResource\Pages;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource;
use App\Domain\Blog\Category\Models\Category;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Category $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'name', 'slug', 'description', 'meta_title', 'meta_description',
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
        /** @var Category $category */
        $category = $this->record;
        $this->syncTranslations($category, $this->form->getState());
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

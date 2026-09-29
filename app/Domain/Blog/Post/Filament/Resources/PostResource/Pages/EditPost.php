<?php

namespace App\Domain\Blog\Post\Filament\Resources\PostResource\Pages;

use App\Domain\Blog\Post\Filament\Concerns\SyncsPostTranslations;
use App\Domain\Blog\Post\Filament\Resources\PostResource;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use SyncsPostTranslations;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Post $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'og_image_id',
                ]);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['translations'], $data['gallery_uploads']);

        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();
        $this->syncPostTranslations($this->record, $state);
        $this->syncPostGallery($this->record, $state);
    }
}

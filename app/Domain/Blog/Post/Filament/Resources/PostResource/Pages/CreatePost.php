<?php

namespace App\Domain\Blog\Post\Filament\Resources\PostResource\Pages;

use App\Domain\Blog\Post\Filament\Concerns\SyncsPostTranslations;
use App\Domain\Blog\Post\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    use SyncsPostTranslations;

    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations'], $data['gallery_uploads']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        $this->syncPostTranslations($this->record, $state);
        $this->syncPostGallery($this->record, $state);
    }
}

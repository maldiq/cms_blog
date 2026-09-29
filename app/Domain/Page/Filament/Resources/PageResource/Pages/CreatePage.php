<?php

namespace App\Domain\Page\Filament\Resources\PageResource\Pages;

use App\Domain\Page\Filament\Concerns\SyncsPageTranslations;
use App\Domain\Page\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use SyncsPageTranslations;

    protected static string $resource = PageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        if (blank($data['user_id'] ?? null)) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncPageTranslations($this->record, $this->form->getState());
    }
}

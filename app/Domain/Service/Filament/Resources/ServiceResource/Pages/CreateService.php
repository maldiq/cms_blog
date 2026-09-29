<?php

namespace App\Domain\Service\Filament\Resources\ServiceResource\Pages;

use App\Domain\Service\Filament\Concerns\SyncsServiceTranslations;
use App\Domain\Service\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use SyncsServiceTranslations;

    protected static string $resource = ServiceResource::class;

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
        $this->syncServiceTranslations($this->record, $this->form->getState());
    }
}

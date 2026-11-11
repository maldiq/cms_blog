<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliatePlatformTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliatePlatform extends CreateRecord
{
    use SyncsAffiliatePlatformTranslations;

    protected static string $resource = AffiliatePlatformResource::class;

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
        $this->syncAffiliatePlatformTranslations($this->record, $this->form->getState());
    }
}

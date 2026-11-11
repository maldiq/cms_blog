<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateCategoryTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliateCategory extends CreateRecord
{
    use SyncsAffiliateCategoryTranslations;

    protected static string $resource = AffiliateCategoryResource::class;

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
        $this->syncAffiliateCategoryTranslations($this->record, $this->form->getState());
    }
}

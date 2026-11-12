<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateProductTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliateProduct extends CreateRecord
{
    use InteractsWithAffiliateProductImage;
    use SyncsAffiliateProductTranslations;

    protected static string $resource = AffiliateProductResource::class;

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
        $this->syncAffiliateProductTranslations($this->record, $this->form->getState());
    }
}

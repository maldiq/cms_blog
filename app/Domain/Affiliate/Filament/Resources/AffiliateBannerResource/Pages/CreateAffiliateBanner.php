<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateBannerTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliateBanner extends CreateRecord
{
    use InteractsWithAffiliateProductImage;
    use SyncsAffiliateBannerTranslations;

    protected static string $resource = AffiliateBannerResource::class;

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
        $this->syncAffiliateBannerTranslations($this->record, $this->form->getState());
    }
}

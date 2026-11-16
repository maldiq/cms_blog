<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliateTrafficSource extends CreateRecord
{
    protected static string $resource = AffiliateTrafficSourceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliateProduct extends CreateRecord
{
    protected static string $resource = AffiliateProductResource::class;

    use InteractsWithAffiliateProductImage;
}

<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateBanners extends ListRecords
{
    protected static string $resource = AffiliateBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

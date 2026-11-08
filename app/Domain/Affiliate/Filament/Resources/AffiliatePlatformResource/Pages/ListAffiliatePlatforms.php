<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAffiliatePlatforms extends ListRecords
{
    protected static string $resource = AffiliatePlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

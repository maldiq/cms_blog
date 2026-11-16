<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateTrafficSources extends ListRecords
{
    protected static string $resource = AffiliateTrafficSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah sumber'),
        ];
    }
}

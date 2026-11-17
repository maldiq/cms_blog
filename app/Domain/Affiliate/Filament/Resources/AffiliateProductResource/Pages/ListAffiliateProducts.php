<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;

use App\Domain\Affiliate\Filament\Pages\AffiliateCatalogImport;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateProducts extends ListRecords
{
    protected static string $resource = AffiliateProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importLinks')
                ->label('Impor link')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->url(fn (): string => AffiliateCatalogImport::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}

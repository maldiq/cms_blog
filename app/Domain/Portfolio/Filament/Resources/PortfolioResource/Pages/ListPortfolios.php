<?php

namespace App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages;

use App\Domain\Portfolio\Filament\Resources\PortfolioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortfolios extends ListRecords
{
    protected static string $resource = PortfolioResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

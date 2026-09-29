<?php

namespace App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages;

use App\Domain\Portfolio\Filament\Resources\PortfolioResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortfolio extends CreateRecord
{
    protected static string $resource = PortfolioResource::class;
}

<?php

namespace App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages;

use App\Domain\Blog\Series\Filament\Resources\SeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeries extends ListRecords
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

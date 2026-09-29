<?php

namespace App\Domain\Blog\Category\Filament\Resources\CategoryResource\Pages;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

<?php

namespace App\Domain\Menu\Filament\Resources\MenuResource\Pages;

use App\Domain\Menu\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;
}

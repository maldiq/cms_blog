<?php

namespace App\Domain\Team\Filament\Resources\TeamResource\Pages;

use App\Domain\Team\Filament\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;
}

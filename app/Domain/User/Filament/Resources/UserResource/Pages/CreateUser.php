<?php

namespace App\Domain\User\Filament\Resources\UserResource\Pages;

use App\Domain\User\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliatePlatform extends EditRecord
{
    protected static string $resource = AffiliatePlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateCategory extends EditRecord
{
    protected static string $resource = AffiliateCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

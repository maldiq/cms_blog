<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateTrafficSource extends EditRecord
{
    protected static string $resource = AffiliateTrafficSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

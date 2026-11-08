<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateProduct extends EditRecord
{
    protected static string $resource = AffiliateProductResource::class;

    use InteractsWithAffiliateProductImage;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

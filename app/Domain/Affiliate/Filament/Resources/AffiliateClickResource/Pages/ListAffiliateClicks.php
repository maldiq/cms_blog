<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateClickResource\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateClickResource;
use App\Domain\Affiliate\Models\AffiliateClick;
use App\Domain\Affiliate\Services\AffiliateClickReportService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListAffiliateClicks extends ListRecords
{
    protected static string $resource = AffiliateClickResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): mixed {
                    Gate::authorize('export', AffiliateClick::class);

                    return app(AffiliateClickReportService::class)
                        ->exportCsv($this->getFilteredTableQuery());
                }),
        ];
    }
}

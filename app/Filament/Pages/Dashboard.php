<?php

namespace App\Filament\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Language\Models\Language;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected ?string $subheading = 'Pantau performa katalog affiliate, klik, dan konten Anda.';

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewSite')
                ->label('Lihat Website')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => url('/'.(Language::getDefault()?->code ?? 'id')))
                ->openUrlInNewTab(),
            Action::make('createProduct')
                ->label('Tambah Produk')
                ->icon('heroicon-o-plus')
                ->url(fn (): string => AffiliateProductResource::getUrl('create')),
        ];
    }
}

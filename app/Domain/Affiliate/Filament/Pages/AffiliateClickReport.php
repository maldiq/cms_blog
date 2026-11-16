<?php

namespace App\Domain\Affiliate\Filament\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateClickResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource;
use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use App\Domain\Affiliate\Services\AffiliateClickReportService;
use Filament\Actions;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class AffiliateClickReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Laporan Klik';

    protected static ?string $title = 'Laporan Klik Affiliate';

    protected static ?string $slug = 'affiliate-click-report';

    protected static string $view = 'filament.pages.affiliate-click-report';

    public int $period = 30;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('affiliate.click.viewAny') ?? false;
    }

    /**
     * @return array<int, string>
     */
    public function getPeriodOptions(): array
    {
        return [
            7 => '7 hari terakhir',
            30 => '30 hari terakhir',
            90 => '90 hari terakhir',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manageSources')
                ->label('Kelola sumber')
                ->icon('heroicon-o-tag')
                ->url(AffiliateTrafficSourceResource::getUrl('index')),
            Actions\Action::make('allClicks')
                ->label('Semua klik')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(AffiliateClickResource::getUrl('index')),
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function sourceLabels(): array
    {
        return AffiliateTrafficSource::labelMap();
    }

    /**
     * @return array{total: int, sources: int, top_source: ?string, top_product: ?string}
     */
    #[Computed]
    public function summary(): array
    {
        return app(AffiliateClickReportService::class)->summary($this->period);
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{source_label: string, clicks: int}>
     */
    #[Computed]
    public function bySource()
    {
        return app(AffiliateClickReportService::class)->bySource($this->period);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Domain\Affiliate\Models\AffiliateClick>
     */
    #[Computed]
    public function byProduct()
    {
        return app(AffiliateClickReportService::class)->byProduct($this->period);
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{locale: string, clicks: int}>
     */
    #[Computed]
    public function byLocale()
    {
        return app(AffiliateClickReportService::class)->byLocale($this->period);
    }
}

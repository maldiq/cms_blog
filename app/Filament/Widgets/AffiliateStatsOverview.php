<?php

namespace App\Filament\Widgets;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AffiliateStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $activeProducts = AffiliateProduct::query()->where('is_active', true)->count();
        $newThisMonth = AffiliateProduct::query()
            ->where('is_active', true)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $totalClicks = (int) AffiliateProduct::query()->sum('click_count');
        $featuredProducts = AffiliateProduct::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->count();
        $activeCategories = AffiliateCategory::query()->where('is_active', true)->count();
        $activePlatforms = AffiliatePlatform::query()->where('is_active', true)->count();

        $averageClicks = $activeProducts > 0 ? (int) round($totalClicks / $activeProducts) : 0;
        $featuredShare = $activeProducts > 0 ? (int) round(($featuredProducts / $activeProducts) * 100) : 0;

        return [
            Stat::make('Produk Aktif', number_format($activeProducts, 0, ',', '.'))
                ->description('+'.number_format($newThisMonth, 0, ',', '.').' produk bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->color('success')
                ->icon('heroicon-o-shopping-bag'),
            Stat::make('Total Klik Affiliate', number_format($totalClicks, 0, ',', '.'))
                ->description('Rata-rata '.number_format($averageClicks, 0, ',', '.').' klik per produk')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('primary')
                ->icon('heroicon-o-cursor-arrow-rays'),
            Stat::make('Produk Unggulan', number_format($featuredProducts, 0, ',', '.'))
                ->description($featuredShare.'% dari katalog aktif')
                ->descriptionIcon('heroicon-m-star')
                ->descriptionColor('warning')
                ->color('warning')
                ->icon('heroicon-o-star'),
            Stat::make('Kategori Aktif', number_format($activeCategories, 0, ',', '.'))
                ->description(number_format($activePlatforms, 0, ',', '.').' marketplace aktif')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info')
                ->icon('heroicon-o-tag'),
        ];
    }
}

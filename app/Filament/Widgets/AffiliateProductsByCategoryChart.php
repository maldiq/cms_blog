<?php

namespace App\Filament\Widgets;

use App\Domain\Affiliate\Models\AffiliateCategory;
use Filament\Widgets\ChartWidget;

class AffiliateProductsByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Produk per Kategori';

    protected static ?string $description = 'Komposisi katalog affiliate yang aktif';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $locale = app()->getLocale();

        $categories = AffiliateCategory::query()
            ->where('is_active', true)
            ->with('translations')
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderByDesc('products_count')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $labels = [];
        $data = [];

        foreach ($categories as $category) {
            $translation = $category->translate($locale, false) ?? $category->translate('id', false);
            $labels[] = $translation?->name ?? 'Tanpa kategori';
            $data[] = $category->products_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Produk',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981',
                        '#6366f1',
                        '#f59e0b',
                        '#ec4899',
                        '#06b6d4',
                        '#8b5cf6',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'cutout' => '68%',
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 8,
                        'padding' => 16,
                    ],
                ],
            ],
        ];
    }
}

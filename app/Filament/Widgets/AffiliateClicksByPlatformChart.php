<?php

namespace App\Filament\Widgets;

use App\Domain\Affiliate\Models\AffiliatePlatform;
use Filament\Widgets\ChartWidget;

class AffiliateClicksByPlatformChart extends ChartWidget
{
    protected static ?string $heading = 'Klik Affiliate per Platform';

    protected static ?string $description = 'Total klik tautan keluar per marketplace';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $locale = app()->getLocale();

        $platforms = AffiliatePlatform::query()
            ->with('translations')
            ->withSum(
                ['products as total_clicks' => fn ($query) => $query->where('is_active', true)],
                'click_count',
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $labels = [];
        $data = [];

        foreach ($platforms as $platform) {
            $translation = $platform->translate($locale, false) ?? $platform->translate('id', false);
            $labels[] = $translation?->name ?? $platform->slug;
            $data[] = (int) ($platform->total_clicks ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Klik',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.65)',
                    'borderColor' => '#059669',
                    'borderWidth' => 1,
                    'borderRadius' => 8,
                    'maxBarThickness' => 38,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.18)'],
                ],
            ],
        ];
    }
}

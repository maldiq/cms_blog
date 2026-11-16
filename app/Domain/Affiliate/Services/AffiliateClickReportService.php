<?php

namespace App\Domain\Affiliate\Services;

use App\Domain\Affiliate\Models\AffiliateClick;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AffiliateClickReportService
{
    /**
     * @param  Builder<AffiliateClick>  $query
     */
    public function exportCsv(Builder $query): StreamedResponse
    {
        $clicks = $query
            ->with(['product.translations', 'product.affiliatePlatform.translations', 'destination'])
            ->orderByDesc('clicked_at')
            ->lazy();

        return response()->streamDownload(function () use ($clicks): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                'click_id',
                'clicked_at',
                'product',
                'platform',
                'destination',
                'source',
                'first_source',
                'locale',
                'referrer_host',
                'user_agent_family',
            ]);

            foreach ($clicks as $click) {
                fputcsv($handle, [
                    $click->click_id,
                    $click->clicked_at?->toDateTimeString(),
                    $this->productTitle($click),
                    $click->product?->affiliatePlatform?->slug,
                    $click->destination?->label,
                    $click->sourceLabel(),
                    $click->first_source,
                    $click->locale,
                    $click->referrer_host,
                    $click->user_agent_family,
                ]);
            }

            fclose($handle);
        }, 'affiliate-clicks-'.now()->format('Y-m-d-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{total: int, sources: int, top_source: ?string, top_product: ?string}
     */
    public function summary(int $days): array
    {
        $total = AffiliateClick::query()->since($days)->count();
        $sources = AffiliateClick::query()->since($days)->whereNotNull('source')->distinct()->count('source');
        $topSource = $this->bySource($days, 1)->first();
        $topProduct = $this->byProduct($days, 1)->first();

        return [
            'total' => $total,
            'sources' => $sources,
            'top_source' => $topSource?->source_label,
            'top_product' => $topProduct !== null ? $this->productTitleFromProduct($topProduct->product) : null,
        ];
    }

    /**
     * @return Collection<int, object{source_label: string, clicks: int}>
     */
    public function bySource(int $days, int $limit = 10): Collection
    {
        return AffiliateClick::query()
            ->selectRaw("COALESCE(source, 'direct') as source_label, COUNT(*) as clicks")
            ->since($days)
            ->groupBy('source_label')
            ->orderByDesc('clicks')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, AffiliateClick>
     */
    public function byProduct(int $days, int $limit = 10): Collection
    {
        return AffiliateClick::query()
            ->selectRaw('affiliate_product_id, COUNT(*) as clicks')
            ->since($days)
            ->with('product.translations')
            ->groupBy('affiliate_product_id')
            ->orderByDesc('clicks')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, object{locale: string, clicks: int}>
     */
    public function byLocale(int $days): Collection
    {
        return AffiliateClick::query()
            ->selectRaw('locale, COUNT(*) as clicks')
            ->since($days)
            ->groupBy('locale')
            ->orderByDesc('clicks')
            ->get();
    }

    private function productTitle(?AffiliateClick $click): string
    {
        return $this->productTitleFromProduct($click?->product);
    }

    private function productTitleFromProduct(?\App\Domain\Affiliate\Models\AffiliateProduct $product): string
    {
        if ($product === null) {
            return '';
        }

        return (string) (
            $product->translate('id', false)?->title
            ?? $product->translate('en', false)?->title
            ?? ''
        );
    }
}

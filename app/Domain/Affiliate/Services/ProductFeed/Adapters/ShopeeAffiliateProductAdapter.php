<?php

namespace App\Domain\Affiliate\Services\ProductFeed\Adapters;

use App\Domain\Affiliate\Data\ProductCandidateData;
use App\Domain\Affiliate\Services\ProductFeed\ProductFeedAdapter;
use App\Domain\Affiliate\Services\ProductFeed\ProductFeedException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Adapter Shopee Affiliate Open API (GraphQL productOfferV2).
 *
 * Skema tanda tangan mengikuti dokumentasi Shopee Affiliate Open API:
 * signature = sha256(app_id + timestamp + payload_json + secret), dikirim
 * sebagai query string bersama app_id dan timestamp.
 *
 * Kredensial didapat dari dashboard Shopee Affiliate setelah aplikasi
 * disetujui. Selama app_id/secret kosong, adapter menolak melakukan request.
 */
class ShopeeAffiliateProductAdapter implements ProductFeedAdapter
{
    public function key(): string
    {
        return 'shopee';
    }

    public function label(): string
    {
        return (string) $this->config('label', 'Shopee Affiliate Open API');
    }

    public function isConfigured(): bool
    {
        return $this->missingConfiguration() === [];
    }

    /**
     * @return list<string>
     */
    public function missingConfiguration(): array
    {
        $missing = [];

        if (blank($this->config('app_id'))) {
            $missing[] = 'SHOPEE_AFFILIATE_APP_ID';
        }

        if (blank($this->config('secret'))) {
            $missing[] = 'SHOPEE_AFFILIATE_APP_SECRET';
        }

        if (blank($this->config('endpoint'))) {
            $missing[] = 'SHOPEE_AFFILIATE_ENDPOINT';
        }

        return $missing;
    }

    /**
     * @return list<ProductCandidateData>
     */
    public function fetch(int $limit = 50): array
    {
        if (! $this->isConfigured()) {
            throw ProductFeedException::notConfigured($this);
        }

        $limit = max(1, min($limit, (int) $this->config('max_limit', 200)));
        $perPage = max(1, min((int) $this->config('per_page', 50), 50));
        $maxPages = max(1, (int) $this->config('max_pages', 5));

        $collected = [];
        $page = 1;

        while (count($collected) < $limit && $page <= $maxPages) {
            $nodes = $this->requestPage($page, $perPage);

            if ($nodes === []) {
                break;
            }

            foreach ($nodes as $node) {
                $candidate = $this->mapNode($node);

                if ($candidate === null) {
                    continue;
                }

                $collected[$candidate->externalId] = $candidate;

                if (count($collected) >= $limit) {
                    break;
                }
            }

            $page++;
        }

        return array_values($collected);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function requestPage(int $page, int $perPage): array
    {
        $variables = array_filter([
            'keyword' => $this->config('keyword') ?: null,
            'limit' => $perPage,
            'page' => $page,
            'sortType' => $this->config('sort_type') ?: null,
        ], fn (mixed $value): bool => $value !== null && $value !== '');

        $payload = json_encode([
            'query' => $this->query(),
            'variables' => $variables,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $appId = (string) $this->config('app_id');
        $timestamp = time();
        $signature = hash('sha256', $appId.$timestamp.$payload.(string) $this->config('secret'));

        $url = (string) $this->config('endpoint').'?'.http_build_query([
            'app_id' => $appId,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ], '', '&', PHP_QUERY_RFC3986);

        $response = Http::timeout((int) $this->config('timeout', 20))
            ->acceptJson()
            ->withBody($payload, 'application/json')
            ->post($url);

        if ($response->failed()) {
            throw ProductFeedException::requestFailed($this, $response->status(), $response->body());
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw ProductFeedException::invalidResponse($this, 'body bukan JSON.');
        }

        $errors = Arr::get($json, 'errors');

        if (is_array($errors) && $errors !== []) {
            $message = (string) (Arr::get($errors, '0.message') ?? 'GraphQL error tanpa pesan.');

            throw ProductFeedException::invalidResponse($this, $message);
        }

        $nodes = Arr::get($json, 'data.productOfferV2.nodes');

        if (! is_array($nodes)) {
            throw ProductFeedException::invalidResponse($this, 'field data.productOfferV2.nodes tidak ada.');
        }

        return array_values(array_filter($nodes, 'is_array'));
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function mapNode(array $node): ?ProductCandidateData
    {
        $externalId = Arr::get($node, 'itemId')
            ?? Arr::get($node, 'productId')
            ?? Arr::get($node, 'item_id');

        $title = Arr::get($node, 'productName')
            ?? Arr::get($node, 'product_name')
            ?? Arr::get($node, 'title');

        if (blank($externalId) || blank($title)) {
            return null;
        }

        return new ProductCandidateData(
            externalId: (string) $externalId,
            platformSlug: 'shopee',
            title: trim((string) $title),
            shopName: $this->nullableString(Arr::get($node, 'shopName')),
            imageUrl: $this->nullableString(Arr::get($node, 'imageUrl')),
            productUrl: $this->nullableString(Arr::get($node, 'productLink')),
            affiliateUrl: $this->nullableString(Arr::get($node, 'offerLink')),
            price: $this->firstNumber($node, ['priceMin', 'price', 'price_min']),
            originalPrice: $this->firstNumber($node, ['originalPrice', 'priceMax', 'price_max']),
            commissionRate: $this->normalizePercent(Arr::get($node, 'commissionRate')),
            soldCount: $this->firstInt($node, ['sales', 'sold', 'soldCount']),
            rating: $this->firstNumber($node, ['ratingStar', 'rating']),
            reviewCount: $this->firstInt($node, ['ratingCount', 'reviewCount']),
            categoryGuess: $this->nullableString(Arr::get($node, 'globalCategoryName')),
            raw: $node,
        );
    }

    private function query(): string
    {
        return <<<'GRAPHQL'
        query productOfferV2($keyword: String, $limit: Int, $page: Int, $sortType: Int) {
          productOfferV2(keyword: $keyword, limit: $limit, page: $page, sortType: $sortType) {
            nodes {
              itemId
              productName
              price
              priceMin
              priceMax
              commissionRate
              sales
              imageUrl
              productLink
              offerLink
              shopName
              ratingStar
              ratingCount
              priceDiscountRate
              globalCategoryName
            }
          }
        }
        GRAPHQL;
    }

    /**
     * Shopee mengirim commissionRate sebagai string. Sebagian respons memakai
     * persen (5.5), sebagian memakai fraksi (0.055). Nilai di bawah 1 dianggap
     * fraksi agar tampilan tetap masuk akal.
     */
    private function normalizePercent(mixed $value): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        if ($number > 0 && $number < 1) {
            $number *= 100;
        }

        return round($number, 2);
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  list<string>  $keys
     */
    private function firstNumber(array $node, array $keys): ?float
    {
        foreach ($keys as $key) {
            $value = Arr::get($node, $key);

            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  list<string>  $keys
     */
    private function firstInt(array $node, array $keys): ?int
    {
        foreach ($keys as $key) {
            $value = Arr::get($node, $key);

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config('affiliate.product_sources.shopee.'.$key, $default);
    }
}

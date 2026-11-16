<?php

namespace App\Domain\Affiliate\Data;

use Illuminate\Support\Arr;

/**
 * Bentuk seragam kandidat produk dari sumber mana pun (API, CSV, manual).
 * Adapter bertugas memetakan respons sumber ke DTO ini.
 */
final readonly class ProductCandidateData
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $externalId,
        public string $platformSlug,
        public string $title,
        public ?string $shopName = null,
        public ?string $imageUrl = null,
        public ?string $productUrl = null,
        public ?string $affiliateUrl = null,
        public ?float $price = null,
        public ?float $originalPrice = null,
        public ?float $commissionRate = null,
        public ?int $soldCount = null,
        public ?float $rating = null,
        public ?int $reviewCount = null,
        public ?string $categoryGuess = null,
        public array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            externalId: (string) Arr::get($data, 'external_id', ''),
            platformSlug: (string) Arr::get($data, 'platform_slug', ''),
            title: trim((string) Arr::get($data, 'title', '')),
            shopName: self::stringOrNull(Arr::get($data, 'shop_name')),
            imageUrl: self::stringOrNull(Arr::get($data, 'image_url')),
            productUrl: self::stringOrNull(Arr::get($data, 'product_url')),
            affiliateUrl: self::stringOrNull(Arr::get($data, 'affiliate_url')),
            price: self::floatOrNull(Arr::get($data, 'price')),
            originalPrice: self::floatOrNull(Arr::get($data, 'original_price')),
            commissionRate: self::floatOrNull(Arr::get($data, 'commission_rate')),
            soldCount: self::intOrNull(Arr::get($data, 'sold_count')),
            rating: self::floatOrNull(Arr::get($data, 'rating')),
            reviewCount: self::intOrNull(Arr::get($data, 'review_count')),
            categoryGuess: self::stringOrNull(Arr::get($data, 'category_guess')),
            raw: (array) Arr::get($data, 'raw', []),
        );
    }

    /**
     * Atribut yang boleh ditimpa saat sync ulang. Status kurasi, catatan tren,
     * dan tautan produk hasil impor sengaja tidak termasuk.
     *
     * @return array<string, mixed>
     */
    public function toSyncAttributes(): array
    {
        return [
            'title' => $this->title,
            'shop_name' => $this->shopName,
            'image_url' => $this->imageUrl,
            'product_url' => $this->productUrl,
            'affiliate_url' => $this->affiliateUrl,
            'price' => $this->price,
            'original_price' => $this->originalPrice,
            'commission_rate' => $this->commissionRate,
            'sold_count' => $this->soldCount,
            'rating' => $this->rating,
            'review_count' => $this->reviewCount,
            'category_guess' => $this->categoryGuess,
            'raw_payload' => $this->raw,
        ];
    }

    public function isValid(): bool
    {
        return $this->externalId !== '' && $this->platformSlug !== '' && $this->title !== '';
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function floatOrNull(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private static function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}

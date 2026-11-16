<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateProductCandidatePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AffiliateProductCandidatePolicy::class)]
class AffiliateProductCandidate extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_SHORTLISTED = 'shortlisted';

    public const STATUS_IMPORTED = 'imported';

    public const STATUS_REJECTED = 'rejected';

    /**
     * @var array<string, string>
     */
    public const STATUS_OPTIONS = [
        self::STATUS_NEW => 'Baru',
        self::STATUS_SHORTLISTED => 'Shortlist',
        self::STATUS_IMPORTED => 'Sudah diimpor',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'source_type',
        'source_name',
        'external_id',
        'platform_slug',
        'title',
        'shop_name',
        'image_url',
        'product_url',
        'affiliate_url',
        'price',
        'original_price',
        'commission_rate',
        'sold_count',
        'rating',
        'review_count',
        'trend_score',
        'trend_notes',
        'category_guess',
        'raw_payload',
        'status',
        'imported_product_id',
        'fetched_at',
        'curated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'sold_count' => 'integer',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'trend_score' => 'decimal:2',
            'raw_payload' => 'array',
            'fetched_at' => 'datetime',
            'curated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<AffiliateProduct, $this>
     */
    public function importedProduct(): BelongsTo
    {
        return $this->belongsTo(AffiliateProduct::class, 'imported_product_id');
    }

    /**
     * @param  Builder<AffiliateProductCandidate>  $query
     * @return Builder<AffiliateProductCandidate>
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Kandidat yang masih menunggu keputusan kurasi.
     *
     * @param  Builder<AffiliateProductCandidate>  $query
     * @return Builder<AffiliateProductCandidate>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_SHORTLISTED]);
    }

    /**
     * @param  Builder<AffiliateProductCandidate>  $query
     * @return Builder<AffiliateProductCandidate>
     */
    public function scopeLatestFetched(Builder $query): Builder
    {
        return $query->orderByDesc('fetched_at')->orderByDesc('id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? (string) $this->status;
    }

    public function bestUrl(): ?string
    {
        return $this->affiliate_url ?: $this->product_url;
    }
}

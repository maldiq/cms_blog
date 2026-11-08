<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateProductPolicy;
use App\Domain\Media\Models\Media;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AffiliateProductPolicy::class)]
class AffiliateProduct extends Model
{
    public const PLATFORMS = [
        'shopee' => 'Shopee',
        'tiktok_shop' => 'TikTok Shop',
        'amazon' => 'Amazon',
        'vps' => 'VPS / Hosting',
        'other' => 'Lainnya',
    ];

    protected $fillable = [
        'image_id',
        'title',
        'slug',
        'category',
        'platform',
        'description',
        'affiliate_url',
        'price',
        'original_price',
        'is_featured',
        'is_active',
        'click_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'click_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    /**
     * @param  Builder<AffiliateProduct>  $query
     * @return Builder<AffiliateProduct>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

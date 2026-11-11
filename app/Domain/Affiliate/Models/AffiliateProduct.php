<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateProductPolicy;
use App\Domain\Media\Models\Media;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AffiliateProductPolicy::class)]
class AffiliateProduct extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = [
        'title',
        'slug',
        'description',
    ];

    protected $translationForeignKey = 'affiliate_product_id';

    protected $translationModel = AffiliateProductTranslation::class;

    protected $fillable = [
        'image_id',
        'affiliate_category_id',
        'affiliate_platform_id',
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
     * @return BelongsTo<AffiliateCategory, $this>
     */
    public function affiliateCategory(): BelongsTo
    {
        return $this->belongsTo(AffiliateCategory::class);
    }

    /**
     * @return BelongsTo<AffiliatePlatform, $this>
     */
    public function affiliatePlatform(): BelongsTo
    {
        return $this->belongsTo(AffiliatePlatform::class);
    }

    /**
     * @param  Builder<AffiliateProduct>  $query
     * @return Builder<AffiliateProduct>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Hanya produk yang punya terjemahan pada locale tersebut, sekaligus eager load.
     *
     * @param  Builder<AffiliateProduct>  $query
     * @return Builder<AffiliateProduct>
     */
    public function scopeLocalized(Builder $query, string $locale): Builder
    {
        return $query
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)]);
    }
}

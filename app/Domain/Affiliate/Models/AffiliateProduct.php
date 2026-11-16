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
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(AffiliateProductPolicy::class)]
class AffiliateProduct extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = [
        'title',
        'slug',
        'excerpt',
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
     * @return HasMany<AffiliateDestination, $this>
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(AffiliateDestination::class)
            ->orderByDesc('is_primary')
            ->orderBy('priority')
            ->orderBy('id');
    }

    /**
     * @return HasMany<AffiliateClick, $this>
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    /**
     * Produk baru otomatis punya satu tujuan utama agar resolver tidak jatuh
     * ke kolom lama. Perubahan affiliate_url juga disinkronkan ke tujuan utama.
     */
    protected static function booted(): void
    {
        static::created(function (self $product): void {
            if (blank($product->affiliate_url)) {
                return;
            }

            $product->destinations()->create([
                'label' => 'Tujuan utama',
                'url' => $product->affiliate_url,
                'status' => AffiliateDestination::STATUS_ACTIVE,
                'is_primary' => true,
                'priority' => 0,
            ]);
        });

        static::updated(function (self $product): void {
            if (! $product->wasChanged('affiliate_url') || blank($product->affiliate_url)) {
                return;
            }

            $primary = $product->destinations()->where('is_primary', true)->first();

            if ($primary !== null) {
                if ($primary->url !== $product->affiliate_url) {
                    $primary->update(['url' => $product->affiliate_url]);
                }

                return;
            }

            $product->destinations()->create([
                'label' => 'Tujuan utama',
                'url' => $product->affiliate_url,
                'status' => AffiliateDestination::STATUS_ACTIVE,
                'is_primary' => true,
                'priority' => 0,
            ]);
        });
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

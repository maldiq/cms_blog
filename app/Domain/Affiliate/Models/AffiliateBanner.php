<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateBannerPolicy;
use App\Domain\Media\Models\Media;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AffiliateBannerPolicy::class)]
class AffiliateBanner extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = [
        'badge',
        'title',
        'subtitle',
        'cta_label',
    ];

    protected $translationForeignKey = 'affiliate_banner_id';

    protected $translationModel = AffiliateBannerTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'image_id',
        'link_url',
        'theme',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<AffiliateBanner>  $query
     * @return Builder<AffiliateBanner>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<AffiliateBanner>  $query
     * @return Builder<AffiliateBanner>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    /**
     * Pilihan gradien untuk slide tanpa gambar.
     *
     * @return array<string, string>
     */
    public static function themeOptions(): array
    {
        return [
            'brand' => 'Biru brand',
            'accent' => 'Oranye promo',
            'emerald' => 'Hijau segar',
            'violet' => 'Ungu premium',
        ];
    }
}

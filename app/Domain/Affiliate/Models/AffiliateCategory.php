<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateCategoryPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(AffiliateCategoryPolicy::class)]
class AffiliateCategory extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = [
        'name',
        'slug',
    ];

    protected $translationForeignKey = 'affiliate_category_id';

    protected $translationModel = AffiliateCategoryTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
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
     * @param  Builder<AffiliateCategory>  $query
     * @return Builder<AffiliateCategory>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<AffiliateCategory>  $query
     * @return Builder<AffiliateCategory>
     */
    public function scopeLocalized(Builder $query, string $locale): Builder
    {
        return $query
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)]);
    }

    /**
     * @return HasMany<AffiliateProduct, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(AffiliateProduct::class);
    }
}

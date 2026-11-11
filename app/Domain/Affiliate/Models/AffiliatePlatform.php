<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliatePlatformPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(AffiliatePlatformPolicy::class)]
class AffiliatePlatform extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = [
        'name',
    ];

    protected $translationForeignKey = 'affiliate_platform_id';

    protected $translationModel = AffiliatePlatformTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
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
     * @param  Builder<AffiliatePlatform>  $query
     * @return Builder<AffiliatePlatform>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return HasMany<AffiliateProduct, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(AffiliateProduct::class);
    }
}

<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCategoryTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'name';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_category_id',
        'locale',
        'name',
        'slug',
    ];

    /**
     * @return BelongsTo<AffiliateCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AffiliateCategory::class, 'affiliate_category_id');
    }
}

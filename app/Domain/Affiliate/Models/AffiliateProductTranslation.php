<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateProductTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'title';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_product_id',
        'locale',
        'title',
        'slug',
        'description',
    ];

    /**
     * @return BelongsTo<AffiliateProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(AffiliateProduct::class, 'affiliate_product_id');
    }
}

<?php

namespace App\Domain\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateBannerTranslation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_banner_id',
        'locale',
        'badge',
        'title',
        'subtitle',
        'cta_label',
    ];

    /**
     * @return BelongsTo<AffiliateBanner, $this>
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(AffiliateBanner::class, 'affiliate_banner_id');
    }
}

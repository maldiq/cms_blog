<?php

namespace App\Domain\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePlatformTranslation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_platform_id',
        'locale',
        'name',
    ];

    /**
     * @return BelongsTo<AffiliatePlatform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(AffiliatePlatform::class, 'affiliate_platform_id');
    }
}

<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateClickPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(AffiliateClickPolicy::class)]
class AffiliateClick extends Model
{
    public const SOURCE_DIRECT = 'direct';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'click_id',
        'affiliate_product_id',
        'affiliate_destination_id',
        'locale',
        'source',
        'first_source',
        'referrer_host',
        'ip_hash',
        'user_agent_family',
        'clicked_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<AffiliateProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(AffiliateProduct::class, 'affiliate_product_id');
    }

    /**
     * @return BelongsTo<AffiliateDestination, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(AffiliateDestination::class, 'affiliate_destination_id');
    }

    /**
     * @param  Builder<AffiliateClick>  $query
     * @return Builder<AffiliateClick>
     */
    public function scopeSince(Builder $query, int $days): Builder
    {
        return $query->where('clicked_at', '>=', now()->subDays($days)->startOfDay());
    }

    public function sourceLabel(): string
    {
        return filled($this->source) ? (string) $this->source : self::SOURCE_DIRECT;
    }
}

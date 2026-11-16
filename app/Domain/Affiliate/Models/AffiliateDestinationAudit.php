<?php

namespace App\Domain\Affiliate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateDestinationAudit extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_destination_id',
        'user_id',
        'action',
        'changes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AffiliateDestination, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(AffiliateDestination::class, 'affiliate_destination_id');
    }

    /**
     * @return BelongsTo<\App\Domain\User\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            default => $this->action,
        };
    }
}

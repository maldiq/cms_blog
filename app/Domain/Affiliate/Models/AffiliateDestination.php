<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateDestinationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(AffiliateDestinationPolicy::class)]
class AffiliateDestination extends Model
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_EXPIRED = 'expired';

    /**
     * @var array<string, string>
     */
    public const STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'Tersedia',
        self::STATUS_OUT_OF_STOCK => 'Stok habis',
        self::STATUS_PAUSED => 'Dijeda',
        self::STATUS_EXPIRED => 'Kedaluwarsa',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'affiliate_product_id',
        'label',
        'url',
        'status',
        'is_primary',
        'priority',
        'starts_at',
        'ends_at',
        'notes',
        'last_checked_at',
        'last_check_status',
    ];

    /**
     * Perubahan yang menunggu dicatat setelah model tersimpan.
     *
     * @var array<string, array{old: mixed, new: mixed}>
     */
    protected array $pendingAuditChanges = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'priority' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'last_check_status' => 'integer',
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
     * @return HasMany<AffiliateDestinationAudit, $this>
     */
    public function audits(): HasMany
    {
        return $this->hasMany(AffiliateDestinationAudit::class)->latest();
    }

    /**
     * @param  Builder<AffiliateDestination>  $query
     * @return Builder<AffiliateDestination>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function isAvailable(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->starts_at !== null && $this->starts_at->isFuture()) {
            return false;
        }

        return ! ($this->ends_at !== null && $this->ends_at->isPast());
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_OUT_OF_STOCK => 'danger',
            self::STATUS_PAUSED => 'warning',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray',
        };
    }

    protected static function booted(): void
    {
        static::creating(function (self $destination): void {
            if ($destination->is_primary === false && ! static::query()
                ->where('affiliate_product_id', $destination->affiliate_product_id)
                ->where('is_primary', true)
                ->exists()) {
                $destination->is_primary = true;
            }
        });

        static::saving(function (self $destination): void {
            if (! $destination->is_primary) {
                return;
            }

            static::query()
                ->where('affiliate_product_id', $destination->affiliate_product_id)
                ->when($destination->exists, fn (Builder $query) => $query->whereKeyNot($destination->getKey()))
                ->update(['is_primary' => false]);
        });

        static::updating(function (self $destination): void {
            $changes = [];

            foreach ($destination->getDirty() as $attribute => $newValue) {
                if (in_array($attribute, ['updated_at', 'last_checked_at', 'last_check_status'], true)) {
                    continue;
                }

                $changes[$attribute] = [
                    'old' => $destination->getOriginal($attribute),
                    'new' => $newValue,
                ];
            }

            $destination->pendingAuditChanges = $changes;
        });

        static::created(function (self $destination): void {
            $destination->writeAudit('created', $destination->only([
                'label', 'url', 'status', 'is_primary', 'priority', 'starts_at', 'ends_at', 'notes',
            ]));
        });

        static::updated(function (self $destination): void {
            if ($destination->pendingAuditChanges === []) {
                return;
            }

            $destination->writeAudit('updated', $destination->pendingAuditChanges);
            $destination->pendingAuditChanges = [];
        });

        static::deleted(function (self $destination): void {
            $destination->writeAudit('deleted', $destination->only([
                'label', 'url', 'status', 'is_primary', 'priority',
            ]));
        });
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function writeAudit(string $action, array $changes): void
    {
        $this->audits()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}

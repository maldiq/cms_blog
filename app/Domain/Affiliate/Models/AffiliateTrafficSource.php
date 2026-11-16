<?php

namespace App\Domain\Affiliate\Models;

use App\Domain\Affiliate\Policies\AffiliateTrafficSourcePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(AffiliateTrafficSourcePolicy::class)]
class AffiliateTrafficSource extends Model
{
    /**
     * Cache label per request agar tabel klik tidak menembak query berulang.
     *
     * @var array<string, string>|null
     */
    protected static ?array $labelCache = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'label',
        'is_active',
        'sort_order',
        'notes',
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
     * @param  Builder<AffiliateTrafficSource>  $query
     * @return Builder<AffiliateTrafficSource>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<AffiliateTrafficSource>  $query
     * @return Builder<AffiliateTrafficSource>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    public function displayName(): string
    {
        return filled($this->label) ? (string) $this->label : (string) $this->code;
    }

    /**
     * Kode sumber yang tersimpan, siap dipakai untuk pilihan/datalist.
     *
     * @return array<string, string>
     */
    public static function codeOptions(bool $onlyActive = true): array
    {
        return static::query()
            ->when($onlyActive, fn (Builder $query) => $query->active())
            ->ordered()
            ->get()
            ->mapWithKeys(fn (self $source): array => [$source->code => $source->displayName()])
            ->all();
    }

    /**
     * Peta kode → label untuk menampilkan log klik lama.
     *
     * @return array<string, string>
     */
    public static function labelMap(): array
    {
        return static::$labelCache ??= static::query()
            ->whereNotNull('label')
            ->pluck('label', 'code')
            ->all();
    }

    public static function flushLabelCache(): void
    {
        static::$labelCache = null;
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            static::flushLabelCache();
        });

        static::deleted(function (): void {
            static::flushLabelCache();
        });
    }
}

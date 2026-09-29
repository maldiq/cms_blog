<?php

namespace App\Domain\Theme\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Theme extends Model
{
    public const CACHE_KEY_CURRENT = 'theme.current';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'author',
        'version',
        'preview_image',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<ThemeSetting, $this>
     */
    public function settings(): HasMany
    {
        return $this->hasMany(ThemeSetting::class);
    }

    /**
     * @param  Builder<Theme>  $query
     * @return Builder<Theme>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function current(): ?self
    {
        return Cache::remember(self::CACHE_KEY_CURRENT, now()->addHour(), function (): ?self {
            return self::query()->active()->orderBy('id')->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_CURRENT);
    }
}

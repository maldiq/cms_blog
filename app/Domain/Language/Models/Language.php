<?php

namespace App\Domain\Language\Models;

use App\Domain\Language\Policies\LanguagePolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

#[UsePolicy(LanguagePolicy::class)]
class Language extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_default',
        'is_active',
        'direction',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Language>  $query
     * @return Builder<Language>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Language>  $query
     * @return Builder<Language>
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public static function getDefault(): ?self
    {
        if (! Schema::hasTable('languages')) {
            return null;
        }

        return static::query()->default()->first()
            ?? static::query()->active()->orderBy('sort_order')->first();
    }

    /**
     * @return Collection<int, Language>
     */
    public static function getActive(): Collection
    {
        if (! Schema::hasTable('languages')) {
            return collect();
        }

        return static::query()
            ->active()
            ->orderBy('sort_order')
            ->get();
    }
}

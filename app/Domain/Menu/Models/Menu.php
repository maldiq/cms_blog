<?php

namespace App\Domain\Menu\Models;

use App\Domain\Menu\Policies\MenuPolicy;
use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(MenuPolicy::class)]
class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
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

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }

    protected static function booted(): void
    {
        static::saving(function (Menu $menu): void {
            if (! $menu->is_active) {
                return;
            }

            static::query()
                ->where('location', $menu->location)
                ->where('is_active', true)
                ->when($menu->exists, fn (Builder $query) => $query->whereKeyNot($menu->getKey()))
                ->update(['is_active' => false]);
        });
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<Menu>  $query
     * @return Builder<Menu>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Menu>  $query
     * @return Builder<Menu>
     */
    public function scopeByLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location);
    }
}

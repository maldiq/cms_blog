<?php

namespace App\Domain\Menu\Models;

use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    /** @use HasFactory<\Database\Factories\MenuItemFactory> */
    use HasFactory;
    use HasTranslations;

    public const TYPE_LINK = 'link';

    public const TYPE_POST = 'post';

    public const TYPE_PAGE = 'page';

    public const TYPE_CATEGORY = 'category';

    public const TYPE_SERIES = 'series';

    public const TYPE_CUSTOM = 'custom';

    /**
     * @var list<string>
     */
    public array $translatable = [
        'label',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'type',
        'target_id',
        'url',
        'label',
        'icon',
        'target',
        'roles',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected static function newFactory(): MenuItemFactory
    {
        return MenuItemFactory::new();
    }

    protected function casts(): array
    {
        return [
            'roles' => 'array',
            'is_active' => 'boolean',
            'target_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsTo<MenuItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @param  Builder<MenuItem>  $query
     * @return Builder<MenuItem>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<MenuItem>  $query
     * @return Builder<MenuItem>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * @param  Builder<MenuItem>  $query
     * @return Builder<MenuItem>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function resolveUrl(): ?string
    {
        $locale = app()->getLocale();

        return match ($this->type) {
            self::TYPE_LINK, self::TYPE_CUSTOM => $this->url,
            self::TYPE_POST => $this->target_id
                ? url("/{$locale}/posts/{$this->target_id}")
                : null,
            self::TYPE_PAGE => $this->target_id
                ? url("/{$locale}/pages/{$this->target_id}")
                : null,
            self::TYPE_CATEGORY => $this->target_id
                ? url("/{$locale}/categories/{$this->target_id}")
                : null,
            self::TYPE_SERIES => $this->target_id
                ? url("/{$locale}/series/{$this->target_id}")
                : null,
            default => $this->url,
        };
    }

    /**
     * Alias accessor untuk Blade / API.
     */
    public function getResolvedUrlAttribute(): ?string
    {
        return $this->resolveUrl();
    }
}

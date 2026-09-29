<?php

namespace App\Domain\Theme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'theme_id',
        'group',
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Theme, $this>
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public static function get(int $themeId, string $group, string $key, mixed $default = null): mixed
    {
        $setting = self::query()
            ->where('theme_id', $themeId)
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if ($setting === null || $setting->value === null) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getGroup(int $themeId, string $group): array
    {
        return self::query()
            ->where('theme_id', $themeId)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->all();
    }

    public static function set(int $themeId, string $group, string $key, mixed $value): void
    {
        self::query()->updateOrCreate(
            [
                'theme_id' => $themeId,
                'group' => $group,
                'key' => $key,
            ],
            [
                'value' => $value,
            ],
        );

        Theme::clearCache();
    }
}

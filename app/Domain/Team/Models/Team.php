<?php

namespace App\Domain\Team\Models;

use App\Domain\Media\Models\Media;
use App\Domain\Team\Policies\TeamPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(TeamPolicy::class)]
class Team extends Model implements TranslatableContract
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = [
        'name',
        'slug',
        'position',
        'bio',
    ];

    protected $translationForeignKey = 'team_id';

    protected $translationModel = TeamTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'photo_id',
        'email',
        'social_links',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): \Database\Factories\TeamFactory
    {
        return \Database\Factories\TeamFactory::new();
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_id');
    }

    /**
     * @param  Builder<Team>  $query
     * @return Builder<Team>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Team>  $query
     * @return Builder<Team>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

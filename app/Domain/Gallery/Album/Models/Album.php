<?php

namespace App\Domain\Gallery\Album\Models;

use App\Domain\Gallery\Album\Policies\AlbumPolicy;
use App\Domain\Media\Models\Media;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(AlbumPolicy::class)]
class Album extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;

    public array $translatedAttributes = [
        'title',
        'slug',
        'description',
    ];

    protected $translationForeignKey = 'album_id';

    protected $translationModel = AlbumTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'cover_id',
        'is_active',
        'sort_order',
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
     * @return BelongsTo<Media, $this>
     */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_id');
    }

    /**
     * @return BelongsToMany<Media, $this>
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'album_media')
            ->withPivot(['id', 'caption', 'sort_order'])
            ->orderByPivot('sort_order');
    }
}

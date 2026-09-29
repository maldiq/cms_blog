<?php

namespace App\Domain\Gallery\Album\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'title';

    public $timestamps = true;

    protected $fillable = [
        'album_id',
        'locale',
        'title',
        'slug',
        'description',
    ];

    /**
     * @return BelongsTo<Album, $this>
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}

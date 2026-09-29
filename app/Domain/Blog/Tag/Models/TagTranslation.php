<?php

namespace App\Domain\Blog\Tag\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'name';

    protected $fillable = [
        'tag_id',
        'locale',
        'name',
        'slug',
    ];

    /**
     * @return BelongsTo<Tag, $this>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}

<?php

namespace App\Domain\Blog\Series\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeriesTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'title';

    protected $fillable = [
        'series_id',
        'locale',
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return BelongsTo<Series, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}

<?php

namespace App\Domain\Blog\Series\Models;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Policies\SeriesPolicy;
use App\Domain\Media\Models\Media;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(SeriesPolicy::class)]
class Series extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;

    protected $table = 'series';

    public array $translatedAttributes = [
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $translationForeignKey = 'series_id';

    protected $translationModel = SeriesTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'cover_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('series_order');
    }
}

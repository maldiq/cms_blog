<?php

namespace App\Domain\Blog\Post\Models;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Comment\Models\Comment;
use App\Domain\Blog\Post\Policies\PostPolicy;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Media\Concerns\RegistersDefaultMediaConversions;
use App\Domain\Media\Models\Media;
use App\Domain\User\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

#[UsePolicy(PostPolicy::class)]
class Post extends Model implements HasMedia, TranslatableContract
{
    use InteractsWithMedia;
    use LogsActivity;
    use RegistersDefaultMediaConversions;
    use SoftDeletes;
    use Translatable;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_REVIEW = 'review';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_ARCHIVED = 'archived';

    public array $translatedAttributes = [
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image_id',
    ];

    protected $translationForeignKey = 'post_id';

    protected $translationModel = PostTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'series_id',
        'series_order',
        'status',
        'type',
        'featured_image_id',
        'template',
        'published_at',
        'is_featured',
        'allow_comment',
        'view_count',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'allow_comment' => 'boolean',
            'view_count' => 'integer',
            'reading_time' => 'integer',
            'series_order' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'type', 'published_at', 'is_featured'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?SpatieMedia $media = null): void
    {
        $this->registerConfiguredImageConversions($media);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Series, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    public function recalculateReadingTime(): void
    {
        $maxWords = 0;

        foreach ($this->translations as $translation) {
            $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($translation->content ?? ''))) ?? '');
            $maxWords = max($maxWords, Str::wordCount($plain));
        }

        $this->reading_time = max(1, (int) ceil($maxWords / 200));
    }
}


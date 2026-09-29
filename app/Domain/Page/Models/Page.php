<?php

namespace App\Domain\Page\Models;

use App\Domain\Media\Concerns\RegistersDefaultMediaConversions;
use App\Domain\Page\Policies\PagePolicy;
use App\Domain\User\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[UsePolicy(PagePolicy::class)]
class Page extends Model implements HasMedia, TranslatableContract
{
    use InteractsWithMedia;
    use RegistersDefaultMediaConversions;
    use SoftDeletes;
    use Translatable;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public array $translatedAttributes = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
    ];

    protected $translationForeignKey = 'page_id';

    protected $translationModel = PageTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'template',
        'is_homepage',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}

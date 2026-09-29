<?php

namespace App\Domain\Blog\Category\Models;

use App\Domain\Blog\Category\Policies\CategoryPolicy;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Media\Models\Media;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(CategoryPolicy::class)]
class Category extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;

    public array $translatedAttributes = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected $translationForeignKey = 'category_id';

    protected $translationModel = CategoryTranslation::class;

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
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category');
    }
}

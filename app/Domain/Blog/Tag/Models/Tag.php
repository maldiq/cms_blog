<?php

namespace App\Domain\Blog\Tag\Models;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Tag\Policies\TagPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(TagPolicy::class)]
class Tag extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;

    public array $translatedAttributes = [
        'name',
        'slug',
    ];

    protected $translationForeignKey = 'tag_id';

    protected $translationModel = TagTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}

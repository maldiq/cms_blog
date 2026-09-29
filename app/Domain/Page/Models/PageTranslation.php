<?php

namespace App\Domain\Page\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'title';

    public $timestamps = true;

    protected $fillable = [
        'page_id',
        'locale',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return BelongsTo<Page, $this>
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}

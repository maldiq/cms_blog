<?php

namespace App\Domain\Portfolio\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'title';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'portfolio_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * @return BelongsTo<Portfolio, $this>
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}

<?php

namespace App\Domain\Portfolio\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioCategoryTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'name';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'portfolio_category_id',
        'locale',
        'name',
        'slug',
    ];

    /**
     * @return BelongsTo<PortfolioCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }
}

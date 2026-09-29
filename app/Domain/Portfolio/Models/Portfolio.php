<?php

namespace App\Domain\Portfolio\Models;

use App\Domain\Media\Models\Media;
use App\Domain\Portfolio\Policies\PortfolioPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(PortfolioPolicy::class)]
class Portfolio extends Model implements TranslatableContract
{
    /** @use HasFactory<\Database\Factories\PortfolioFactory> */
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = [
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $translationForeignKey = 'portfolio_id';

    protected $translationModel = PortfolioTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'cover_id',
        'client_name',
        'project_date',
        'project_url',
        'category_id',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_date' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): \Database\Factories\PortfolioFactory
    {
        return \Database\Factories\PortfolioFactory::new();
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_id');
    }

    /**
     * @return BelongsTo<PortfolioCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }

    /**
     * @param  Builder<Portfolio>  $query
     * @return Builder<Portfolio>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Portfolio>  $query
     * @return Builder<Portfolio>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  Builder<Portfolio>  $query
     * @return Builder<Portfolio>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

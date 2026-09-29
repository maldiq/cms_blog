<?php

namespace App\Domain\Portfolio\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioCategory extends Model implements TranslatableContract
{
    use Translatable;

    public array $translatedAttributes = ['name', 'slug'];

    protected $translationForeignKey = 'portfolio_category_id';

    protected $translationModel = PortfolioCategoryTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
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
     * @return HasMany<Portfolio, $this>
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'category_id');
    }
}

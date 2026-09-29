<?php

namespace App\Domain\Service\Models;

use App\Domain\Media\Models\Media;
use App\Domain\Service\Policies\ServicePolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(ServicePolicy::class)]
class Service extends Model implements TranslatableContract
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
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

    protected $translationForeignKey = 'service_id';

    protected $translationModel = ServiceTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'icon',
        'cover_id',
        'price_from',
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
            'price_from' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): \Database\Factories\ServiceFactory
    {
        return \Database\Factories\ServiceFactory::new();
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_id');
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

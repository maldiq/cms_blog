<?php

namespace App\Domain\Testimonial\Models;

use App\Domain\Media\Models\Media;
use App\Domain\Testimonial\Policies\TestimonialPolicy;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(TestimonialPolicy::class)]
class Testimonial extends Model implements TranslatableContract
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = [
        'author_name',
        'author_position',
        'author_company',
        'content',
    ];

    protected $translationForeignKey = 'testimonial_id';

    protected $translationModel = TestimonialTranslation::class;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'photo_id',
        'rating',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function newFactory(): \Database\Factories\TestimonialFactory
    {
        return \Database\Factories\TestimonialFactory::new();
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'photo_id');
    }

    /**
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

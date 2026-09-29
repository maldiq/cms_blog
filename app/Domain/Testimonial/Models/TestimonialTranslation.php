<?php

namespace App\Domain\Testimonial\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestimonialTranslation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'testimonial_id',
        'locale',
        'author_name',
        'author_position',
        'author_company',
        'content',
    ];

    /**
     * @return BelongsTo<Testimonial, $this>
     */
    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(Testimonial::class);
    }
}

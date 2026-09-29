<?php

namespace App\Domain\Media\Models;

use App\Domain\Media\Policies\MediaPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\Translatable\HasTranslations;

#[UsePolicy(MediaPolicy::class)]
class Media extends BaseMedia
{
    use HasTranslations;

    /**
     * @var list<string>
     */
    public array $translatable = [
        'title',
        'alt_text',
        'caption',
    ];

}

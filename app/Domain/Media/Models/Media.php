<?php

namespace App\Domain\Media\Models;

use App\Domain\Media\Policies\MediaPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\Translatable\HasTranslations;

#[UsePolicy(MediaPolicy::class)]
class Media extends BaseMedia
{
    use HasTranslations;

    /**
     * Keep legacy image uploads with generic MIME metadata discoverable,
     * without treating files explicitly identified as non-images as images.
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->where('mime_type', 'like', 'image/%')
                ->orWhere(function (Builder $query): void {
                    $query->where(function (Builder $query): void {
                        $query->whereNull('mime_type')
                            ->orWhereIn('mime_type', ['application/octet-stream', 'binary/octet-stream']);
                    })->where(function (Builder $query): void {
                        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp', 'tif', 'tiff'] as $extension) {
                            $query->orWhereRaw('LOWER(file_name) LIKE ?', ['%.'.$extension]);
                        }
                    });
                });
        });
    }

    /**
     * @var list<string>
     */
    public array $translatable = [
        'title',
        'alt_text',
        'caption',
    ];
}

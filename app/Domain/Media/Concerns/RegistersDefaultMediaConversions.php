<?php

namespace App\Domain\Media\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait RegistersDefaultMediaConversions
{
    protected function registerConfiguredImageConversions(?Media $media = null): void
    {
        /** @var array<string, array{width: int, height: int}> $conversions */
        $conversions = config('media-library.image_conversions', []);

        foreach ($conversions as $name => $dimensions) {
            $width = (int) ($dimensions['width'] ?? 0);
            $height = (int) ($dimensions['height'] ?? 0);

            if ($width <= 0 || $height <= 0) {
                continue;
            }

            $this->addMediaConversion($name)
                ->fit(Fit::Contain, $width, $height)
                ->performOnCollections('*')
                ->nonQueued();
        }
    }
}

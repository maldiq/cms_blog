<?php

namespace App\Domain\Blog\Post\Filament\Concerns;

use App\Domain\Media\Filament\Concerns\InteractsWithMediaImagePicker;

/**
 * Alias lama untuk halaman Post; logika picker ada di domain Media.
 */
trait InteractsWithPostImagePicker
{
    use InteractsWithMediaImagePicker;
}

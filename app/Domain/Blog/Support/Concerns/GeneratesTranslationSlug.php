<?php

namespace App\Domain\Blog\Support\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesTranslationSlug
{
    protected static function bootGeneratesTranslationSlug(): void
    {
        static::saving(function (Model $translation): void {
            $source = $translation->slugSourceAttribute();

            if (blank($translation->getAttribute('slug')) && filled($translation->getAttribute($source))) {
                $translation->setAttribute('slug', Str::slug((string) $translation->getAttribute($source)));
            }
        });
    }

    protected function slugSourceAttribute(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }
}

<?php

use App\Domain\Media\Models\Media;

if (! function_exists('media_responsive_image')) {
    /**
     * Atribut src/srcset untuk gambar MediaLibrary (konversi medium/large).
     *
     * @return array{src: ?string, srcset: ?string, sizes: string, loading: string}
     */
    function media_responsive_image(?Media $media, bool $lazy = true): array
    {
        if ($media === null) {
            return [
                'src' => null,
                'srcset' => null,
                'sizes' => '(max-width: 768px) 100vw, 896px',
                'loading' => $lazy ? 'lazy' : 'eager',
            ];
        }

        $srcsetParts = [];

        if ($media->hasGeneratedConversion('medium')) {
            $srcsetParts[] = $media->getFullUrl('medium').' 800w';
        }

        if ($media->hasGeneratedConversion('large')) {
            $srcsetParts[] = $media->getFullUrl('large').' 1600w';
        }

        $src = $media->hasGeneratedConversion('large')
            ? $media->getFullUrl('large')
            : ($media->hasGeneratedConversion('medium')
                ? $media->getFullUrl('medium')
                : $media->getFullUrl());

        return [
            'src' => $src,
            'srcset' => $srcsetParts !== [] ? implode(', ', $srcsetParts) : null,
            'sizes' => '(max-width: 768px) 100vw, 896px',
            'loading' => $lazy ? 'lazy' : 'eager',
        ];
    }
}

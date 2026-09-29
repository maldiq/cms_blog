<?php

namespace App\Domain\Theme\Http\Controllers;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThemeAssetController
{
    /**
     * Serve preview/screenshot theme dari public atau resources/views/themes.
     */
    public function __invoke(string $slug, string $file): BinaryFileResponse|Response
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug)) ?? '';
        $file = basename(str_replace('\\', '/', $file));

        if ($slug === '' || $file === '') {
            abort(404);
        }

        if (! preg_match('/^preview\.(svg|jpg|jpeg|png|webp)$/i', $file)) {
            abort(404);
        }

        $candidates = [
            public_path('themes/'.$slug.'/'.$file),
            resource_path('views/themes/'.$slug.'/'.$file),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return response()->file($path, [
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        abort(404);
    }
}

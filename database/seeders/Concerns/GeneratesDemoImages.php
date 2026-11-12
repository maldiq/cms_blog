<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait GeneratesDemoImages
{
    /**
     * Buat gambar demo gradient dua warna di disk public dan kembalikan
     * path relatifnya untuk diunggah ke perpustakaan media.
     *
     * @param  array{0: int, 1: int, 2: int}  $from
     * @param  array{0: int, 1: int, 2: int}  $to
     */
    protected function generateDemoImage(string $filename, int $width, int $height, array $from, array $to): string
    {
        $path = 'seed/'.ltrim($filename, '/');
        $fullPath = Storage::disk('public')->path($path);
        $directory = dirname($fullPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (is_file($fullPath)) {
            return $path;
        }

        $image = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y++) {
            $ratio = $height > 1 ? $y / ($height - 1) : 0.0;
            $color = imagecolorallocate(
                $image,
                (int) round($from[0] + ($to[0] - $from[0]) * $ratio),
                (int) round($from[1] + ($to[1] - $from[1]) * $ratio),
                (int) round($from[2] + ($to[2] - $from[2]) * $ratio),
            );
            imageline($image, 0, $y, $width, $y, $color);
        }

        imagealphablending($image, true);

        foreach (
            [
                [$width * 0.82, $height * 0.22, $width * 0.30, 90],
                [$width * 0.15, $height * 0.82, $width * 0.26, 96],
                [$width * 0.45, $height * 0.10, $width * 0.18, 110],
            ] as [$centerX, $centerY, $diameter, $alpha]
        ) {
            imagefilledellipse(
                $image,
                (int) $centerX,
                (int) $centerY,
                (int) $diameter,
                (int) $diameter,
                imagecolorallocatealpha($image, 255, 255, 255, $alpha),
            );
        }

        imagejpeg($image, $fullPath, 85);
        imagedestroy($image);

        return $path;
    }
}

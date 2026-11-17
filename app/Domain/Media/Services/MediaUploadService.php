<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Models\MediaLibraryItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MediaUploadService
{
    /**
     * Unduh gambar dari URL lalu simpan ke perpustakaan media. Dipakai saat
     * impor produk dari CSV agar admin tidak perlu mengunggah satu per satu.
     */
    public function uploadFromUrl(string $url, string $collection = 'default'): Media
    {
        if (! in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            throw new InvalidArgumentException('URL gambar harus http atau https.');
        }

        $response = Http::timeout(30)->get($url);

        if ($response->failed()) {
            throw new InvalidArgumentException('Gambar tidak bisa diunduh (HTTP '.$response->status().').');
        }

        $contentType = strtolower((string) $response->header('Content-Type'));

        if (! str_starts_with($contentType, 'image/') || str_contains($contentType, 'svg')) {
            throw new InvalidArgumentException('URL bukan gambar yang didukung ('.$contentType.').');
        }

        $body = $response->body();

        if ($body === '' || strlen($body) > 10 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran gambar kosong atau melebihi 10 MB.');
        }

        $extension = match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            str_contains($contentType, 'gif') => 'gif',
            str_contains($contentType, 'avif') => 'avif',
            default => 'jpg',
        };

        $path = 'imports/'.Str::ulid().'.'.$extension;
        Storage::disk('public')->put($path, $body);

        try {
            return $this->uploadFromPath($path, $collection);
        } finally {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Upload file ke perpustakaan media (koleksi default).
     */
    public function uploadFromPath(string $pathOnPublicDisk, string $collection = 'default'): Media
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($pathOnPublicDisk)) {
            throw new InvalidArgumentException("File tidak ditemukan: {$pathOnPublicDisk}");
        }

        return MediaLibraryItem::library()
            ->addMedia($disk->path($pathOnPublicDisk))
            ->toMediaCollection($collection);
    }

    public function uploadFile(UploadedFile $file, string $collection = 'default'): Media
    {
        return MediaLibraryItem::library()
            ->addMedia($file)
            ->toMediaCollection($collection);
    }

    /**
     * @param  list<string>  $pathsOnPublicDisk
     * @return list<int>
     */
    public function uploadManyFromPublicPaths(array $pathsOnPublicDisk, string $collection = 'default'): array
    {
        $ids = [];

        foreach ($pathsOnPublicDisk as $path) {
            if (blank($path)) {
                continue;
            }

            $ids[] = $this->uploadFromPath($path, $collection)->id;
        }

        return $ids;
    }
}

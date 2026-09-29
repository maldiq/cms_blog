<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Models\MediaLibraryItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    /**
     * Upload file ke perpustakaan media (koleksi default).
     */
    public function uploadFromPath(string $pathOnPublicDisk, string $collection = 'default'): Media
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($pathOnPublicDisk)) {
            throw new \InvalidArgumentException("File tidak ditemukan: {$pathOnPublicDisk}");
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

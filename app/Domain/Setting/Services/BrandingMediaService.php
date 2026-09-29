<?php

namespace App\Domain\Setting\Services;

use App\Domain\Media\Models\Media;
use App\Domain\Setting\Models\SiteBranding;
use Illuminate\Support\Facades\Storage;

class BrandingMediaService
{
    public function storeFromUpload(?string $uploadPath, string $collection): ?int
    {
        if (blank($uploadPath)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($uploadPath)) {
            return null;
        }

        $branding = SiteBranding::instance();
        $branding->clearMediaCollection($collection);

        $media = $branding
            ->addMedia($disk->path($uploadPath))
            ->toMediaCollection($collection);

        return $media->id;
    }

    public function uploadPathForMediaId(?int $mediaId): ?string
    {
        if ($mediaId === null) {
            return null;
        }

        $media = Media::query()->find($mediaId);

        if ($media === null) {
            return null;
        }

        return str_replace(storage_path('app/public/'), '', $media->getPath());
    }
}

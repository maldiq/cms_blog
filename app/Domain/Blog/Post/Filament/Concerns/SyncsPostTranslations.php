<?php

namespace App\Domain\Blog\Post\Filament\Concerns;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Services\MediaUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait SyncsPostTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncPostTranslations(Post $post, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $translation = $post->translateOrNew($locale);
            $translation->fill([
                'title' => $bucket['title'] ?? '',
                'slug' => $bucket['slug'] ?? null,
                'excerpt' => $bucket['excerpt'] ?? null,
                'content' => $bucket['content'] ?? null,
                'meta_title' => $bucket['meta_title'] ?? null,
                'meta_description' => $bucket['meta_description'] ?? null,
                'meta_keywords' => $bucket['meta_keywords'] ?? null,
                'og_image_id' => $bucket['og_image_id'] ?? null,
            ]);
            $translation->save();
        }

        $post->load('translations');
        $post->recalculateReadingTime();
        $post->saveQuietly();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncPostGallery(Post $post, array $data): void
    {
        $paths = (array) ($data['gallery_uploads'] ?? []);

        if ($paths === []) {
            return;
        }

        $disk = Storage::disk('public');

        foreach ($paths as $path) {
            if (blank($path) || ! $disk->exists($path)) {
                continue;
            }

            $post->addMedia($disk->path($path))->toMediaCollection('gallery');
        }
    }

    /**
     * Upload lampiran RichEditor ke media library, kembalikan URL publik.
     */
    protected function storeEditorUpload(mixed $file): ?string
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $media = app(MediaUploadService::class)->uploadFile($file, 'default');

        return $media->getFullUrl();
    }
}

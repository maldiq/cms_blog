<?php

namespace App\Domain\Media\Filament\Forms\Components;

use App\Domain\Media\Services\MediaUploadService;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Editor konten kaya (TipTap) dengan dukungan tabel.
 *
 * Semua gambar yang diunggah dari editor masuk ke Media library lewat
 * MediaUploadService, jadi tidak ada file "liar" di storage publik.
 */
class RichContentEditor extends TiptapEditor
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->output(TiptapOutput::Html)
            ->tools([
                'heading', 'bullet-list', 'ordered-list', '|',
                'bold', 'italic', 'underline', 'strike', 'code', '|',
                'link', 'media', 'table', '|',
                'blockquote', 'hr', 'code-block',
            ])
            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                $media = app(MediaUploadService::class)->uploadFile($file);

                return $media->getFullUrl();
            });
    }
}

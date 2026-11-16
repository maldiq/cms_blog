<?php

namespace App\Domain\Media\Filament\Concerns;

use App\Domain\Media\Models\Media;
use Livewire\Attributes\On;

/**
 * Menyimpan gambar yang dipilih dari media picker ke state form Filament.
 *
 * statePath adalah path state Filament, mis. "data.featured_image_id" atau
 * "data.cover_id", sehingga picker yang sama bisa dipakai untuk beberapa
 * field dalam satu halaman.
 */
trait InteractsWithMediaImagePicker
{
    #[On('media-image-picked')]
    public function setPickedMedia(?int $mediaId, ?string $statePath = null): void
    {
        $this->authorizeAccess();

        if (blank($statePath)) {
            return;
        }

        if ($mediaId !== null) {
            abort_unless(
                Media::query()
                    ->images()
                    ->whereKey($mediaId)
                    ->exists(),
                404,
            );
        }

        $path = str_starts_with($statePath, 'data.')
            ? substr($statePath, strlen('data.'))
            : $statePath;

        $data = $this->data ?? [];
        data_set($data, $path, $mediaId);
        $this->data = $data;
    }
}

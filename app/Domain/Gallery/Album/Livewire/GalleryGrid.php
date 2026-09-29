<?php

namespace App\Domain\Gallery\Album\Livewire;

use App\Domain\Gallery\Album\Models\Album;
use Livewire\Component;

class GalleryGrid extends Component
{
    public int $albumId;

    public string $locale;

    public function render()
    {
        $album = Album::query()
            ->with(['media' => fn ($query) => $query->orderByPivot('sort_order')])
            ->findOrFail($this->albumId);

        $items = $album->media->map(function ($media) {
            return [
                'id' => $media->id,
                'thumb' => $media->hasGeneratedConversion('medium')
                    ? $media->getUrl('medium')
                    : $media->getUrl(),
                'full' => $media->getUrl('large'),
                'caption' => $media->pivot->caption,
                'name' => $media->name,
            ];
        });

        return view('domain.gallery.livewire.gallery-grid', [
            'items' => $items,
        ]);
    }
}

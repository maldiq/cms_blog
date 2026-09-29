<?php

namespace App\Domain\Media\Livewire;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class MediaPicker extends Component
{
    use WithFileUploads;

    public bool $showModal = false;

    public bool $multiple = true;

    /**
     * @var list<int>
     */
    public array $selectedMediaIds = [];

    /**
     * @var list<TemporaryUploadedFile>
     */
    public array $newUploads = [];

    public string $search = '';

    public ?string $collectionFilter = null;

    public function mount(bool $multiple = true, array $selectedMediaIds = []): void
    {
        $this->multiple = $multiple;
        $this->selectedMediaIds = array_values(array_map('intval', $selectedMediaIds));
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('newUploads', 'search', 'collectionFilter');
    }

    /**
     * @return LengthAwarePaginator<int, Media>
     */
    public function getMediaItemsProperty(): LengthAwarePaginator
    {
        return Media::query()
            ->when(filled($this->search), function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when(filled($this->collectionFilter), function ($query): void {
                $query->where('collection_name', $this->collectionFilter);
            })
            ->where('mime_type', 'like', 'image/%')
            ->latest()
            ->paginate(12);
    }

    public function toggleSelection(int $mediaId): void
    {
        if ($this->multiple) {
            if (in_array($mediaId, $this->selectedMediaIds, true)) {
                $this->selectedMediaIds = array_values(array_diff($this->selectedMediaIds, [$mediaId]));
            } else {
                $this->selectedMediaIds[] = $mediaId;
            }

            return;
        }

        $this->selectedMediaIds = [$mediaId];
    }

    public function uploadNew(MediaUploadService $mediaUploadService): void
    {
        $this->validate([
            'newUploads' => ['required', 'array', 'min:1'],
            'newUploads.*' => ['image', 'max:10240'],
        ]);

        foreach ($this->newUploads as $file) {
            $media = $mediaUploadService->uploadFile($file);
            $this->selectedMediaIds[] = $media->id;
        }

        $this->selectedMediaIds = array_values(array_unique($this->selectedMediaIds));
        $this->reset('newUploads');

        $this->dispatch('notify', message: 'Upload selesai');
    }

    public function confirmSelection(): void
    {
        $ids = array_values(array_unique($this->selectedMediaIds));

        if (! $this->multiple && count($ids) > 1) {
            $ids = [reset($ids)];
        }

        $this->dispatch('media-picker-selected', mediaIds: $ids);
        $this->closeModal();
    }

    public function previewUrl(Media $media): string
    {
        if ($media->hasGeneratedConversion('thumb')) {
            return $media->getFullUrl('thumb');
        }

        return $media->getFullUrl();
    }

    public function render(): View
    {
        return view('domain.media.livewire.media-picker');
    }
}

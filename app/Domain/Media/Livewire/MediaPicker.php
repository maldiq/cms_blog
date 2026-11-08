<?php

namespace App\Domain\Media\Livewire;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaPicker extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;

    public bool $multiple = true;

    public ?string $statePath = null;

    public string $activeTab = 'library';

    public ?int $focusedMediaId = null;

    /**
     * @var list<int>
     */
    public array $pendingMediaIds = [];

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

    public function mount(bool $multiple = true, array $selectedMediaIds = [], ?string $statePath = null): void
    {
        $this->multiple = $multiple;
        $this->selectedMediaIds = array_values(array_map('intval', $selectedMediaIds));
        $this->pendingMediaIds = $this->selectedMediaIds;
        $this->statePath = $statePath;

        abort_unless(auth()->user()?->can('viewAny', Media::class), 403);
    }

    public function openModal(): void
    {
        $this->authorizeMediaAccess();
        $this->showModal = true;
        $this->activeTab = 'library';
        $this->reset('search', 'collectionFilter');
        $this->resetPage('mediaPickerPage');
        $this->pendingMediaIds = $this->selectedMediaIds;
        $this->focusedMediaId = $this->pendingMediaIds[0] ?? null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset('newUploads', 'search', 'collectionFilter', 'activeTab');
        $this->pendingMediaIds = $this->selectedMediaIds;
        $this->resetPage('mediaPickerPage');
    }

    /**
     * @return LengthAwarePaginator<int, Media>
     */
    public function getMediaItemsProperty(): LengthAwarePaginator
    {
        return Media::query()
            ->images()
            ->when(filled($this->search), function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when(filled($this->collectionFilter), function ($query): void {
                $query->where('collection_name', $this->collectionFilter);
            })
            ->latest()
            ->paginate(12, ['*'], 'mediaPickerPage');
    }

    /**
     * @return Collection<int, string>
     */
    public function getCollectionsProperty(): Collection
    {
        return Media::query()
            ->images()
            ->select('collection_name')
            ->distinct()
            ->orderBy('collection_name')
            ->pluck('collection_name');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('mediaPickerPage');
    }

    public function updatedCollectionFilter(): void
    {
        $this->resetPage('mediaPickerPage');
    }

    public function toggleSelection(int $mediaId): void
    {
        abort_unless($this->findImage($mediaId) !== null, 404);

        if ($this->multiple) {
            if (in_array($mediaId, $this->pendingMediaIds, true)) {
                $this->pendingMediaIds = array_values(array_diff($this->pendingMediaIds, [$mediaId]));
            } else {
                $this->pendingMediaIds[] = $mediaId;
            }
        } else {
            $this->pendingMediaIds = [$mediaId];
        }

        $this->focusedMediaId = $mediaId;
    }

    public function uploadNew(MediaUploadService $mediaUploadService): void
    {
        $this->authorizeMediaUpload();
        $this->validate([
            'newUploads' => ['required', 'array', 'min:1', 'max:10'],
            'newUploads.*' => ['image', 'max:10240'],
        ]);

        $uploadedIds = [];
        foreach ($this->newUploads as $file) {
            $media = $mediaUploadService->uploadFile($file);
            $uploadedIds[] = $media->id;
        }

        if ($this->multiple) {
            $this->pendingMediaIds = array_values(array_unique([...$this->pendingMediaIds, ...$uploadedIds]));
        } elseif ($uploadedIds !== []) {
            $this->pendingMediaIds = [end($uploadedIds)];
        }

        $this->focusedMediaId = $this->pendingMediaIds[0] ?? null;
        $this->activeTab = 'library';
        $this->reset('newUploads', 'search', 'collectionFilter');
        $this->resetPage('mediaPickerPage');
    }

    public function confirmSelection(): void
    {
        $ids = array_values(array_unique($this->pendingMediaIds));

        foreach ($ids as $id) {
            abort_unless($this->findImage($id) !== null, 404);
        }

        if (! $this->multiple && count($ids) > 1) {
            $ids = [reset($ids)];
        }

        $this->selectedMediaIds = $ids;
        $this->dispatch('media-picker-selected', mediaIds: $ids);

        if ($this->statePath !== null && ! $this->multiple) {
            $this->dispatch('affiliate-image-picked', mediaId: $ids[0] ?? null);
        }

        $this->closeModal();
    }

    public function clearSelection(): void
    {
        $this->selectedMediaIds = [];
        $this->pendingMediaIds = [];
        $this->focusedMediaId = null;
        $this->dispatch('media-picker-selected', mediaIds: []);

        if ($this->statePath !== null && ! $this->multiple) {
            $this->dispatch('affiliate-image-picked', mediaId: null);
        }
    }

    public function getFocusedMediaProperty(): ?Media
    {
        return $this->findImage($this->focusedMediaId);
    }

    public function getSelectedMediaProperty(): ?Media
    {
        return $this->findImage($this->selectedMediaIds[0] ?? null);
    }

    public function previewUrl(Media $media): string
    {
        if ($media->hasGeneratedConversion('thumb')) {
            return $media->getFullUrl('thumb');
        }

        return $media->getFullUrl();
    }

    private function findImage(?int $mediaId): ?Media
    {
        if ($mediaId === null) {
            return null;
        }

        return Media::query()
            ->images()
            ->find($mediaId);
    }

    private function authorizeMediaAccess(): void
    {
        abort_unless(auth()->user()?->can('viewAny', Media::class), 403);
    }

    private function authorizeMediaUpload(): void
    {
        abort_unless(auth()->user()?->can('media.upload'), 403);
    }

    public function render(): View
    {
        return view('domain.media.livewire.media-picker');
    }
}

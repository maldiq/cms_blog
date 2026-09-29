<?php

namespace App\Support\Concerns;

use Livewire\Attributes\On;

trait HasMediaPicker
{
    public bool $mediaPickerOpen = false;

    /**
     * @var list<int>
     */
    public array $selectedMediaIds = [];

    public function openMediaPicker(): void
    {
        $this->mediaPickerOpen = true;
        $this->dispatch('open-media-picker');
    }

    public function closeMediaPicker(): void
    {
        $this->mediaPickerOpen = false;
    }

    /**
     * @param  list<int>|array<int, int>  $mediaIds
     */
    #[On('media-picker-selected')]
    public function handleMediaPickerSelected(array $mediaIds): void
    {
        $this->selectedMediaIds = array_values(array_unique(array_map('intval', $mediaIds)));
        $this->mediaPickerOpen = false;
        $this->onMediaPickerSelected($this->selectedMediaIds);
    }

    /**
     * Hook setelah user memilih media dari picker.
     *
     * @param  list<int>  $mediaIds
     */
    protected function onMediaPickerSelected(array $mediaIds): void
    {
        //
    }
}

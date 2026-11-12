@php
    $statePath = $getStatePath();
    $selectedMediaIds = filled($getState()) ? [(int) $getState()] : [];
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div>
        @livewire(
            'app.domain.media.livewire.media-picker',
            [
                'multiple' => false,
                'selectedMediaIds' => $selectedMediaIds,
                'statePath' => $statePath,
            ],
            key('media-image-picker-' . $statePath)
        )
    </div>
</x-dynamic-component>

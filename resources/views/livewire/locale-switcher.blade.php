<div class="flex items-center gap-2">
    @foreach ($this->languages as $language)
        <button
            type="button"
            wire:click="switchLocale('{{ $language->code }}')"
            @class([
                'rounded-md px-3 py-1 text-sm font-medium transition',
                'bg-emerald-600 text-white' => app()->getLocale() === $language->code,
                'bg-gray-200 text-gray-700 hover:bg-gray-300' => app()->getLocale() !== $language->code,
            ])
        >
            @if ($language->flag)
                <span class="mr-1">{{ $language->flag }}</span>
            @endif
            {{ strtoupper($language->code) }}
        </button>
    @endforeach
</div>

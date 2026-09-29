<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="grid grid-cols-1 items-center gap-10 rounded-2xl border border-gray-200 bg-gray-50 p-8 lg:grid-cols-2 lg:p-12">
            <div class="space-y-3">
                @if (filled($title))
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>

            <div>
                @livewire(\App\Domain\Newsletter\Livewire\NewsletterForm::class, [
                    'placeholder' => $placeholder,
                    'buttonLabel' => $buttonLabel,
                ])
            </div>
        </div>
    </div>
</section>

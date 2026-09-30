@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="__('messages.contact_us')" :subtitle="theme('contact.address')" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 lg:grid-cols-2 lg:px-6">
            <div class="space-y-4 text-gray-700">
                @if (filled(theme('contact.address')))
                    <p><span class="font-semibold text-gray-900">{{ __('messages.contact_address') }}:</span> {{ theme('contact.address') }}</p>
                @endif
                @if (filled(theme('contact.phone')))
                    <p><span class="font-semibold text-gray-900">{{ __('messages.contact_phone') }}:</span> {{ theme('contact.phone') }}</p>
                @endif
                @if (filled(theme('contact.email')))
                    <p><span class="font-semibold text-gray-900">{{ __('messages.contact_email') }}:</span> {{ theme('contact.email') }}</p>
                @endif
                @if (filled(theme('contact.working_hours')))
                    <p><span class="font-semibold text-gray-900">{{ __('messages.working_hours') }}:</span> {{ theme('contact.working_hours') }}</p>
                @endif
            </div>

            <div>
                @livewire(\App\Domain\Contact\Livewire\ContactForm::class)
            </div>
        </div>

        @if (filled(theme('contact.map_embed')))
            <div class="mx-auto mt-10 max-w-7xl px-4 lg:px-6">
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    {!! theme('contact.map_embed') !!}
                </div>
            </div>
        @endif
    </section>
@endsection

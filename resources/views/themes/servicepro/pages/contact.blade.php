@extends('layouts.app')

@section('content')
    <section class="border-b border-slate-200 bg-white">
        <div class="container-page py-7 sm:py-9">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="transition hover:text-brand-800">{{ __('messages.home') }}</a>
                <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                <span class="font-medium text-slate-700">{{ __('messages.contact_us') }}</span>
            </nav>

            <div class="mt-5">
                <p class="section-eyebrow">{{ __('messages.contact_eyebrow') }}</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ __('messages.contact_us') }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.contact_page_intro') }}</p>
            </div>
        </div>
    </section>

    <div class="container-page py-8 sm:py-10">
        <div class="grid gap-5 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-3">
                @if (filled(theme('contact.address')))
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ __('messages.contact_address') }}</p>
                            <p class="mt-0.5 text-sm leading-6 text-slate-600">{{ theme('contact.address') }}</p>
                        </div>
                    </div>
                @endif

                @if (filled(theme('contact.phone')))
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ __('messages.contact_phone') }}</p>
                            <a href="tel:{{ preg_replace('/\s+/', '', theme('contact.phone')) }}" class="mt-0.5 block text-sm font-medium leading-6 text-brand-800 transition hover:text-accent-600">{{ theme('contact.phone') }}</a>
                        </div>
                    </div>
                @endif

                @if (filled(theme('contact.email')))
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ __('messages.contact_email') }}</p>
                            <a href="mailto:{{ theme('contact.email') }}" class="mt-0.5 block truncate text-sm font-medium leading-6 text-brand-800 transition hover:text-accent-600">{{ theme('contact.email') }}</a>
                        </div>
                    </div>
                @endif

                @if (filled(theme('contact.working_hours')))
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ __('messages.working_hours') }}</p>
                            <p class="mt-0.5 text-sm leading-6 text-slate-600">{{ theme('contact.working_hours') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ __('messages.contact_form_title') }}</h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">{{ __('messages.contact_form_intro') }}</p>
                <div class="mt-6">
                    @livewire(\App\Domain\Contact\Livewire\ContactForm::class)
                </div>
            </div>
        </div>

        @if (filled(theme('contact.map_embed')))
            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                {!! theme('contact.map_embed') !!}
            </div>
        @endif
    </div>
@endsection

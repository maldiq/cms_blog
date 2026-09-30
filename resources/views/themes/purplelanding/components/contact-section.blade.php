@php
    $leadFormTitle = theme_locale('hero.lead_form_title') ?? __('messages.hero_lead_title');
@endphp
<section id="contact" class="scroll-mt-24 bg-gradient-to-br from-violet-700 via-violet-800 to-fuchsia-800 py-16 text-white lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-violet-200">{{ __('messages.purple_contact_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ theme_locale('contact.section_title') ?? __('messages.contact_us') }}</h2>
                <p class="mt-4 text-lg text-violet-100">{{ theme_locale('contact.section_subtitle') ?? __('messages.hero_lead_subtitle') }}</p>

                <dl class="mt-8 space-y-4 text-sm text-violet-100">
                    @if (filled(theme('contact.address')))
                        <div>
                            <dt class="font-semibold text-white">{{ __('messages.contact_address') }}</dt>
                            <dd class="mt-1">{{ theme('contact.address') }}</dd>
                        </div>
                    @endif
                    @if (filled(theme('contact.phone')))
                        <div>
                            <dt class="font-semibold text-white">{{ __('messages.contact_phone') }}</dt>
                            <dd class="mt-1">{{ theme('contact.phone') }}</dd>
                        </div>
                    @endif
                    @if (filled(theme('contact.email')))
                        <div>
                            <dt class="font-semibold text-white">{{ __('messages.contact_email') }}</dt>
                            <dd class="mt-1"><a href="mailto:{{ theme('contact.email') }}" class="hover:text-white">{{ theme('contact.email') }}</a></dd>
                        </div>
                    @endif
                    @if (filled(theme('contact.working_hours')))
                        <div>
                            <dt class="font-semibold text-white">{{ __('messages.working_hours') }}</dt>
                            <dd class="mt-1">{{ theme('contact.working_hours') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl bg-white p-6 text-gray-900 shadow-2xl sm:p-8">
                <h3 class="text-xl font-semibold text-gray-900">{{ $leadFormTitle }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('messages.hero_lead_subtitle') }}</p>
                <div class="mt-6">
                    @livewire(\App\Domain\Contact\Livewire\HeroLeadForm::class)
                </div>
            </div>
        </div>
    </div>
</section>

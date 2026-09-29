<?php

namespace App\Domain\Newsletter\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';

    public ?string $placeholder = null;

    public ?string $buttonLabel = null;

    public function mount(?string $placeholder = null, ?string $buttonLabel = null): void
    {
        $this->placeholder = $placeholder;
        $this->buttonLabel = $buttonLabel;
    }

    public function subscribe(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Langkah berikutnya: simpan ke tabel subscribers / integrasi ESP.
        session()->flash('newsletter_subscribed', true);

        $this->reset('email');
    }

    public function render(): View
    {
        return view('domain.newsletter.livewire.newsletter-form');
    }
}

<?php

namespace App\Domain\Contact\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        session()->flash('contact_sent', true);

        $this->reset(['name', 'email', 'subject', 'message']);
    }

    public function render(): View
    {
        return view('domain.contact.livewire.contact-form');
    }
}

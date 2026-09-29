<?php

namespace App\Domain\Contact\Livewire;

use App\Domain\Contact\Services\ContactSubmissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    /** Honeypot — harus kosong */
    public string $website = '';

    public bool $submitted = false;

    public ?string $errorMessage = null;

    public function submit(ContactSubmissionService $contactSubmissionService): void
    {
        $this->errorMessage = null;
        $this->submitted = false;

        if (filled($this->website)) {
            $this->reset(['name', 'email', 'phone', 'subject', 'message', 'website']);
            $this->submitted = true;

            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [], [
            'name' => 'nama',
            'email' => 'email',
            'phone' => 'telepon',
            'subject' => 'subjek',
            'message' => 'pesan',
        ]);

        $ip = request()->ip() ?? '0.0.0.0';
        $rateKey = 'contact-submit:'.$ip;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $this->errorMessage = __('messages.contact_rate_limit');

            return;
        }

        RateLimiter::hit($rateKey, 60);

        $contactSubmissionService->submit([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => filled($this->phone) ? $this->phone : null,
            'subject' => $this->subject,
            'message' => $this->message,
            'locale' => app()->getLocale(),
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'message', 'website']);
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('domain.contact.livewire.contact-form');
    }
}

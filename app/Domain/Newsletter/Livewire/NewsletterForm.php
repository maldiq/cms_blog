<?php

namespace App\Domain\Newsletter\Livewire;

use App\Domain\Newsletter\Services\NewsletterSubscriberService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';

    public ?string $placeholder = null;

    public ?string $buttonLabel = null;

    public bool $subscribed = false;

    public ?string $errorMessage = null;

    public function mount(?string $placeholder = null, ?string $buttonLabel = null): void
    {
        $this->placeholder = $placeholder;
        $this->buttonLabel = $buttonLabel;
    }

    public function subscribe(NewsletterSubscriberService $subscriberService): void
    {
        $this->errorMessage = null;
        $this->subscribed = false;

        $this->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
        ], [], [
            'email' => 'email',
        ]);

        $ip = request()->ip() ?? '0.0.0.0';
        $rateKey = 'newsletter-subscribe:'.$ip;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $this->errorMessage = __('messages.newsletter_rate_limit');

            return;
        }

        RateLimiter::hit($rateKey, 60);

        $subscriberService->subscribe([
            'email' => $this->email,
            'locale' => app()->getLocale(),
            'ip_address' => $ip,
        ]);

        $this->reset('email');
        $this->subscribed = true;
    }

    public function render(): View
    {
        return view('domain.newsletter.livewire.newsletter-form');
    }
}

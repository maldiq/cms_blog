<?php

namespace App\Domain\Newsletter\Services;

use App\Domain\Newsletter\Models\NewsletterSubscriber;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberService
{
    /**
     * @param  array{email: string, locale: string, ip_address: ?string}  $data
     */
    public function subscribe(array $data): NewsletterSubscriber
    {
        return NewsletterSubscriber::query()->create([
            'email' => $data['email'],
            'locale' => $data['locale'],
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'ip_address' => $data['ip_address'],
        ]);
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): void
    {
        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * @param  Collection<int, NewsletterSubscriber>|iterable<int, NewsletterSubscriber>  $subscribers
     */
    public function exportCsv(iterable $subscribers, string $filename = 'newsletter-subscribers.csv'): StreamedResponse
    {
        return response()->streamDownload(function () use ($subscribers): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['email', 'locale', 'is_active', 'subscribed_at', 'unsubscribed_at', 'ip_address']);

            foreach ($subscribers as $subscriber) {
                fputcsv($handle, [
                    $subscriber->email,
                    $subscriber->locale,
                    $subscriber->is_active ? '1' : '0',
                    $subscriber->subscribed_at?->toDateTimeString(),
                    $subscriber->unsubscribed_at?->toDateTimeString(),
                    $subscriber->ip_address,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

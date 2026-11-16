<?php

namespace App\Domain\Affiliate\Services;

use App\Domain\Affiliate\Models\AffiliateClick;
use App\Domain\Affiliate\Models\AffiliateDestination;
use App\Domain\Affiliate\Models\AffiliateProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AffiliateClickLogger
{
    /**
     * Catat satu klik keluar. Bot/link preview, rate limit, dan klik ganda
     * dalam beberapa detik tidak dicatat agar statistik tidak menggelembung.
     */
    public function log(
        Request $request,
        AffiliateProduct $product,
        ?AffiliateDestination $destination,
        string $locale,
        ?string $source,
        ?string $firstSource,
    ): ?AffiliateClick {
        if ($this->isBot($request->userAgent())) {
            return null;
        }

        $rateKey = 'affiliate-go:'.sha1((string) $request->ip());
        $maxAttempts = max(1, (int) config('affiliate.click.rate_limit_per_minute', 30));

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            return null;
        }

        RateLimiter::hit($rateKey, 60);

        $dedupeKey = 'affiliate_clicked:'.$product->getKey();
        $lastClickedAt = (int) session($dedupeKey);
        $dedupeSeconds = max(0, (int) config('affiliate.click.dedupe_seconds', 3));

        if ($lastClickedAt > 0 && now()->getTimestamp() - $lastClickedAt < $dedupeSeconds) {
            return null;
        }

        session([$dedupeKey => now()->getTimestamp()]);

        $click = DB::transaction(function () use ($request, $product, $destination, $locale, $source, $firstSource): AffiliateClick {
            $click = AffiliateClick::query()->create([
                'click_id' => (string) Str::ulid(),
                'affiliate_product_id' => $product->getKey(),
                'affiliate_destination_id' => $destination?->getKey(),
                'locale' => $locale,
                'source' => $source ?: AffiliateClick::SOURCE_DIRECT,
                'first_source' => $firstSource ?: $source,
                'referrer_host' => $this->referrerHost($request),
                'ip_hash' => $this->ipHash($request),
                'user_agent_family' => $this->userAgentFamily($request->userAgent()),
                'clicked_at' => now(),
            ]);

            $product->increment('click_count');

            return $click;
        });

        return $click;
    }

    private function isBot(?string $userAgent): bool
    {
        if (blank($userAgent)) {
            return false;
        }

        return preg_match(
            '/(bot|crawler|spider|preview|facebookexternalhit|whatsapp|telegram|slurp|bingpreview|headless|curl|wget|python-requests|httpclient|axios|go-http-client|java\/|libwww|scrapy|phantomjs|semrush|ahrefs|uptime|pingdom)/i',
            $userAgent,
        ) === 1;
    }

    private function referrerHost(Request $request): ?string
    {
        $referer = $request->headers->get('referer');

        if (! is_string($referer) || $referer === '') {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? Str::limit(strtolower($host), 255, '') : null;
    }

    private function ipHash(Request $request): ?string
    {
        $ip = $request->ip();

        if ($ip === null || $ip === '') {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }

    private function userAgentFamily(?string $userAgent): ?string
    {
        if (blank($userAgent)) {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Edg') => 'Edge',
            str_contains($userAgent, 'OPR') || str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'Other',
        };
    }
}

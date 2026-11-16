<?php

namespace App\Domain\Affiliate\Services;

use Illuminate\Http\Request;

class TrafficSource
{
    public const SESSION_LAST = 'affiliate_source';

    public const SESSION_FIRST = 'affiliate_source_first';

    public const COOKIE_LAST = 'np_src';

    public const COOKIE_FIRST = 'np_src_first';

    /**
     * Ambil `?src=` dari request, normalisasi, lalu simpan sebagai last-touch.
     * First-touch hanya diisi sekali selama cookie masih berlaku.
     */
    public function capture(Request $request): ?string
    {
        $this->hydrateSession($request);

        $raw = $request->query('src');

        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $source = $this->normalize($raw);

        if ($source === null) {
            return null;
        }

        $this->store($request, $source);

        return $source;
    }

    public function last(Request $request): ?string
    {
        $this->hydrateSession($request);

        $value = session(self::SESSION_LAST);

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function first(Request $request): ?string
    {
        $this->hydrateSession($request);

        $value = session(self::SESSION_FIRST);

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $source = strtolower(trim($value));
        $source = preg_replace('/[^a-z0-9_-]+/', '_', $source) ?? '';
        $source = trim($source, '_-');

        if ($source === '') {
            return null;
        }

        $max = max(8, (int) config('affiliate.tracking.max_source_length', 64));

        return strlen($source) > $max ? substr($source, 0, $max) : $source;
    }

    private function store(Request $request, string $source): void
    {
        $first = session(self::SESSION_FIRST);

        if (! is_string($first) || $first === '') {
            $first = $request->cookie(self::COOKIE_FIRST);
        }

        if (! is_string($first) || $first === '') {
            $first = $source;
            session([self::SESSION_FIRST => $first]);
            $this->queueCookie($request, self::COOKIE_FIRST, $first);
        }

        session([self::SESSION_LAST => $source]);
        $this->queueCookie($request, self::COOKIE_LAST, $source);
    }

    private function hydrateSession(Request $request): void
    {
        if (! session()->has(self::SESSION_LAST)) {
            $last = $request->cookie(self::COOKIE_LAST);

            if (is_string($last) && $last !== '') {
                session([self::SESSION_LAST => $last]);
            }
        }

        if (! session()->has(self::SESSION_FIRST)) {
            $first = $request->cookie(self::COOKIE_FIRST);

            if (is_string($first) && $first !== '') {
                session([self::SESSION_FIRST => $first]);
            }
        }
    }

    private function queueCookie(Request $request, string $name, string $value): void
    {
        $days = max(1, (int) config('affiliate.tracking.cookie_days', 30));

        cookie()->queue(cookie(
            name: $name,
            value: $value,
            minutes: $days * 24 * 60,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'lax',
        ));
    }
}

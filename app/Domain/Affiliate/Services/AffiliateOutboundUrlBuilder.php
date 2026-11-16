<?php

namespace App\Domain\Affiliate\Services;

class AffiliateOutboundUrlBuilder
{
    public function __construct(
        private readonly TrafficSource $trafficSource,
    ) {}

    /**
     * Tambahkan parameter sub-ID marketplace tanpa merusak query atau fragment
     * yang sudah ada. Platform yang tidak dikenal dibiarkan apa adanya.
     */
    public function build(string $url, ?string $platformSlug, ?string $source): string
    {
        $source = $this->trafficSource->normalize($source);

        if ($source === null || blank($platformSlug)) {
            return $url;
        }

        $config = config('affiliate.tracking.platforms.'.$platformSlug);

        if (! is_array($config) || blank($config['param'] ?? null)) {
            return $url;
        }

        $parts = parse_url($url);

        if (! is_array($parts)
            || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || blank($parts['host'] ?? null)) {
            return $url;
        }

        parse_str($parts['query'] ?? '', $query);

        $maxLength = max(8, (int) ($config['max_length'] ?? 64));
        $query[$config['param']] = $this->fitSource($source, $maxLength);

        $authority = $parts['host'];

        if (filled($parts['user'] ?? null)) {
            $authority = $parts['user']
                .(filled($parts['pass'] ?? null) ? ':'.$parts['pass'] : '')
                .'@'.$authority;
        }

        if (filled($parts['port'] ?? null)) {
            $authority .= ':'.$parts['port'];
        }

        $rebuilt = strtolower($parts['scheme']).'://'.$authority.($parts['path'] ?? '');
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        if ($queryString !== '') {
            $rebuilt .= '?'.$queryString;
        }

        if (filled($parts['fragment'] ?? null)) {
            $rebuilt .= '#'.$parts['fragment'];
        }

        return $rebuilt;
    }

    private function fitSource(string $source, int $maxLength): string
    {
        if (strlen($source) <= $maxLength) {
            return $source;
        }

        return substr($source, 0, $maxLength - 9).'-'.substr(sha1($source), 0, 8);
    }
}

<?php

namespace App\Domain\Affiliate\Services\ProductFeed;

use RuntimeException;

class ProductFeedException extends RuntimeException
{
    public static function notConfigured(ProductFeedAdapter $adapter): self
    {
        $missing = implode(', ', $adapter->missingConfiguration());

        return new self(sprintf(
            'Sumber "%s" belum dikonfigurasi. Isi variabel berikut di .env: %s.',
            $adapter->label(),
            $missing !== '' ? $missing : '(tidak diketahui)',
        ));
    }

    public static function requestFailed(ProductFeedAdapter $adapter, int $status, string $body): self
    {
        $body = trim(strip_tags($body));

        if (mb_strlen($body) > 300) {
            $body = mb_substr($body, 0, 300).'...';
        }

        return new self(sprintf(
            'Sumber "%s" menolak permintaan (HTTP %d). %s',
            $adapter->label(),
            $status,
            $body,
        ));
    }

    public static function invalidResponse(ProductFeedAdapter $adapter, string $reason): self
    {
        return new self(sprintf('Respons "%s" tidak bisa dibaca: %s', $adapter->label(), $reason));
    }
}

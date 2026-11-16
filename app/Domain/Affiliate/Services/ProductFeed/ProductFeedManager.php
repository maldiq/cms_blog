<?php

namespace App\Domain\Affiliate\Services\ProductFeed;

use InvalidArgumentException;

class ProductFeedManager
{
    /**
     * @return array<string, ProductFeedAdapter>
     */
    public function all(): array
    {
        $adapters = [];

        foreach ((array) config('affiliate.product_sources', []) as $key => $source) {
            $class = is_array($source) ? ($source['adapter'] ?? null) : null;

            if (! is_string($class) || ! class_exists($class)) {
                continue;
            }

            $adapter = app($class);

            if (! $adapter instanceof ProductFeedAdapter) {
                throw new InvalidArgumentException(sprintf(
                    'Adapter %s untuk sumber "%s" harus mengimplementasikan %s.',
                    $class,
                    $key,
                    ProductFeedAdapter::class,
                ));
            }

            $adapters[$key] = $adapter;
        }

        return $adapters;
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    public function get(string $key): ProductFeedAdapter
    {
        return $this->all()[$key] ?? throw new InvalidArgumentException(sprintf(
            'Sumber produk "%s" tidak dikenal. Pilihan: %s.',
            $key,
            implode(', ', $this->keys()) ?: '(belum ada)',
        ));
    }

    public function firstConfigured(): ?ProductFeedAdapter
    {
        foreach ($this->all() as $adapter) {
            if ($adapter->isConfigured()) {
                return $adapter;
            }
        }

        return null;
    }
}

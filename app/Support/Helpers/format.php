<?php

if (! function_exists('affiliate_currency')) {
    /**
     * Konfigurasi mata uang tampilan untuk locale aktif.
     *
     * @return array{code: string, symbol: string, rate: float|int, decimals: int, decimal_separator: string, thousands_separator: string, symbol_position: string, symbol_space: bool}
     */
    function affiliate_currency(?string $locale = null): array
    {
        $defaults = [
            'code' => 'IDR',
            'symbol' => 'Rp',
            'rate' => 1,
            'decimals' => 0,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'before',
            'symbol_space' => true,
        ];

        $locale ??= app()->getLocale();
        $config = config('affiliate.currencies.'.$locale);

        if (! is_array($config)) {
            $fallback = config('affiliate.currencies.'.config('app.locale'))
                ?? config('affiliate.currencies.id')
                ?? [];

            $config = is_array($fallback) ? $fallback : [];
        }

        return array_merge($defaults, $config);
    }
}

if (! function_exists('affiliate_convert_price')) {
    /**
     * Konversi nominal dari base currency ke mata uang locale.
     */
    function affiliate_convert_price(float|int|string|null $amount, ?string $locale = null): ?float
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $currency = affiliate_currency($locale);

        return (float) $amount * (float) $currency['rate'];
    }
}

if (! function_exists('format_price')) {
    /**
     * Format harga memakai simbol, desimal, dan pemisah sesuai locale.
     */
    function format_price(float|int|string|null $amount, ?string $locale = null): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $currency = affiliate_currency($locale);
        $value = (float) $amount * (float) $currency['rate'];

        $number = number_format(
            $value,
            (int) $currency['decimals'],
            (string) $currency['decimal_separator'],
            (string) $currency['thousands_separator'],
        );

        $symbol = trim((string) $currency['symbol']);

        if ($symbol === '') {
            return $number;
        }

        $space = $currency['symbol_space'] ? ' ' : '';

        return $currency['symbol_position'] === 'after'
            ? $number.$space.$symbol
            : $symbol.$space.$number;
    }
}

if (! function_exists('format_rupiah')) {
    /**
     * Format Rupiah (dipertahankan untuk pemakaian lama).
     */
    function format_rupiah(float|int|string|null $amount): ?string
    {
        return format_price($amount, 'id');
    }
}

if (! function_exists('affiliate_discount_percent')) {
    /**
     * Persentase diskon dari harga asli, null bila tidak ada diskon.
     */
    function affiliate_discount_percent(float|int|string|null $price, float|int|string|null $originalPrice): ?int
    {
        $price = $price !== null && $price !== '' ? (float) $price : null;
        $originalPrice = $originalPrice !== null && $originalPrice !== '' ? (float) $originalPrice : null;

        if ($price === null || $originalPrice === null || $price <= 0 || $originalPrice <= $price) {
            return null;
        }

        return (int) round((($originalPrice - $price) / $originalPrice) * 100);
    }
}

if (! function_exists('affiliate_category_url')) {
    /**
     * URL kategori dengan segmen path yang sesuai locale (id: kategori, lainnya: category).
     */
    function affiliate_category_url(string $locale, ?string $slug): ?string
    {
        if ($slug === null || trim($slug) === '') {
            return null;
        }

        $routeName = $locale === 'id' ? 'products.category' : 'products.category.intl';

        return route($routeName, [
            'locale' => $locale,
            'slug' => $slug,
        ]);
    }
}

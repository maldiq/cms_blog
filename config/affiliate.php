<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fallback hero slides
    |--------------------------------------------------------------------------
    |
    | Digunakan bila admin belum membuat banner aktif di /kelola/affiliate-banners.
    | Setiap slide: badge, title, subtitle, theme (brand|accent|emerald|violet),
    | cta_label, dan link (null = halaman semua produk).
    |
    */
    'hero' => [
        'fallback_slides' => [
            [
                'badge' => 'Kurasi mingguan',
                'title' => 'Belanja pintar, hemat setiap hari.',
                'subtitle' => 'Produk pilihan dari Shopee, TikTok Shop, Amazon, dan lainnya — dicek satu per satu sebelum masuk katalog.',
                'theme' => 'brand',
                'cta_label' => 'Jelajahi Produk',
                'link' => null,
            ],
            [
                'badge' => 'Promo marketplace',
                'title' => 'Bandingkan harga tanpa pindah tab.',
                'subtitle' => 'Temukan penawaran terbaik, harga sebelum diskon, dan tautan resmi marketplace dalam satu halaman.',
                'theme' => 'accent',
                'cta_label' => 'Lihat Produk Unggulan',
                'link' => null,
            ],
            [
                'badge' => 'Baru minggu ini',
                'title' => 'Temuan terbaru, langsung dari toko.',
                'subtitle' => 'Katalog diperbarui setiap hari. Simpan halaman ini dan temukan produk baru sebelum ramai dibicarakan.',
                'theme' => 'emerald',
                'cta_label' => 'Lihat Produk Baru',
                'link' => null,
            ],
        ],

        'fallback_slides_en' => [
            [
                'badge' => 'Weekly picks',
                'title' => 'Shop smart, save every day.',
                'subtitle' => 'Handpicked products from Shopee, TikTok Shop, Amazon, and more — checked one by one before entering the catalog.',
                'theme' => 'brand',
                'cta_label' => 'Explore Products',
                'link' => null,
            ],
            [
                'badge' => 'Marketplace promo',
                'title' => 'Compare prices without switching tabs.',
                'subtitle' => 'Find the best offers, original prices, and official marketplace links on a single page.',
                'theme' => 'accent',
                'cta_label' => 'View Featured Products',
                'link' => null,
            ],
            [
                'badge' => 'New this week',
                'title' => 'Fresh finds, straight from the store.',
                'subtitle' => 'The catalog is updated daily. Bookmark this page and discover new products before they trend.',
                'theme' => 'emerald',
                'cta_label' => 'View New Products',
                'link' => null,
            ],
        ],

        'featured_limit' => 4,

        'new_limit' => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Mata uang tampilan per locale
    |--------------------------------------------------------------------------
    |
    | Harga produk disimpan dalam base_currency (IDR). Nilai `rate` adalah
    | pengali dari base currency ke mata uang locale. Contoh: 16.000 IDR = 1 USD,
    | maka rate USD = 1 / 16.000 = 0.0000625.
    |
    */
    'base_currency' => 'IDR',

    'currencies' => [
        'id' => [
            'code' => 'IDR',
            'symbol' => 'Rp',
            'rate' => 1,
            'decimals' => 0,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'before',
            'symbol_space' => true,
        ],
        'en' => [
            'code' => 'USD',
            'symbol' => '$',
            'rate' => 0.0000625,
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'symbol_position' => 'before',
            'symbol_space' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tampilan kartu produk
    |--------------------------------------------------------------------------
    */
    'badges' => [
        'shopee' => ['label' => 'Shopee', 'class' => 'bg-orange-500/95 text-white'],
        'tiktok_shop' => ['label' => 'TikTok Shop', 'class' => 'bg-slate-900/95 text-white'],
        'amazon' => ['label' => 'Amazon', 'class' => 'bg-amber-500/95 text-white'],
        'vps' => ['label' => 'VPS / Hosting', 'class' => 'bg-indigo-600/95 text-white'],
        'other' => ['label' => 'Lainnya', 'class' => 'bg-slate-500/95 text-white'],
    ],
];

<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Services\MediaUploadService;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateProductSeeder extends Seeder
{
    use GeneratesDemoImages;

    /**
     * Jumlah produk contoh pertama yang ditandai unggulan agar section
     * "Pilihan NainPicks" dan kartu hero terisi saat data diaktifkan.
     */
    private const FEATURED_DEMO_COUNT = 4;

    /**
     * @var list<array{title: string, slug: string, category: string, platform: string, description: string, affiliate_url: string, price: int, original_price: int}>
     */
    private array $products = [
        [
            'title' => 'Contoh: Headphone Bluetooth',
            'slug' => 'contoh-headphone-bluetooth-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
            'price' => 249000,
            'original_price' => 349000,
        ],
        [
            'title' => 'Contoh: Charger dan Aksesori Gadget',
            'slug' => 'contoh-aksesori-gadget-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
            'price' => 89000,
            'original_price' => 129000,
        ],
        [
            'title' => 'Contoh: Peralatan Rumah Tangga',
            'slug' => 'contoh-peralatan-rumah-shopee',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
            'price' => 159000,
            'original_price' => 199000,
        ],
        [
            'title' => 'Contoh: Organizer Meja Minimalis',
            'slug' => 'contoh-organizer-meja-tiktok',
            'category' => 'Rumah Tangga',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
            'price' => 79000,
            'original_price' => 109000,
        ],
        [
            'title' => 'Contoh: Perlengkapan Dapur',
            'slug' => 'contoh-perlengkapan-dapur-tiktok',
            'category' => 'Dapur',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
            'price' => 199000,
            'original_price' => 259000,
        ],
        [
            'title' => 'Contoh: Aksesori Kecantikan',
            'slug' => 'contoh-aksesori-kecantikan-tiktok',
            'category' => 'Kecantikan',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
            'price' => 129000,
            'original_price' => 169000,
        ],
        [
            'title' => 'Contoh: Buku dan Referensi',
            'slug' => 'contoh-buku-amazon',
            'category' => 'Buku',
            'platform' => 'amazon',
            'description' => 'Data contoh untuk katalog Amazon. Ganti dengan produk dan tautan Amazon Associates milik Anda.',
            'affiliate_url' => 'https://www.amazon.com/',
            'price' => 145000,
            'original_price' => 180000,
        ],
        [
            'title' => 'Contoh: Aksesori Komputer',
            'slug' => 'contoh-aksesori-komputer-amazon',
            'category' => 'Elektronik',
            'platform' => 'amazon',
            'description' => 'Data contoh untuk katalog Amazon. Ganti dengan produk dan tautan Amazon Associates milik Anda.',
            'affiliate_url' => 'https://www.amazon.com/',
            'price' => 299000,
            'original_price' => 399000,
        ],
        [
            'title' => 'Contoh: VPS untuk Website',
            'slug' => 'contoh-vps-hostinger',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan VPS. Ganti dengan URL kampanye affiliate VPS Anda.',
            'affiliate_url' => 'https://www.hostinger.com/vps-hosting',
            'price' => 65000,
            'original_price' => 82000,
        ],
        [
            'title' => 'Contoh: Cloud VPS untuk Developer',
            'slug' => 'contoh-cloud-vps-digitalocean',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan cloud VPS. Ganti dengan URL kampanye affiliate Anda.',
            'affiliate_url' => 'https://www.digitalocean.com/pricing/droplets',
            'price' => 88000,
            'original_price' => 105000,
        ],
        [
            'title' => 'Contoh: VPS untuk Aplikasi',
            'slug' => 'contoh-vps-vultr',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan VPS. Ganti dengan URL kampanye affiliate VPS Anda.',
            'affiliate_url' => 'https://www.vultr.com/pricing/',
            'price' => 95000,
            'original_price' => 120000,
        ],
        [
            'title' => 'Contoh: Perangkat Lunak Produktivitas',
            'slug' => 'contoh-software-produktivitas',
            'category' => 'Software',
            'platform' => 'other',
            'description' => 'Data contoh untuk program affiliate lain. Ganti dengan produk dan tautan resmi Anda.',
            'affiliate_url' => 'https://www.canva.com/',
            'price' => 349000,
            'original_price' => 429000,
        ],
        [
            'title' => 'Contoh: Perangkat Jaringan',
            'slug' => 'contoh-perangkat-jaringan-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
            'price' => 319000,
            'original_price' => 419000,
        ],
    ];

    /**
     * Palet warna gambar demo, dipakai bergiliran per produk.
     *
     * @var list<array{0: array{0: int, 1: int, 2: int}, 1: array{0: int, 1: int, 2: int}}>
     */
    private array $imagePalettes = [
        [[15, 23, 42], [37, 99, 235]],
        [[6, 44, 34], [16, 185, 129]],
        [[67, 33, 11], [234, 179, 8]],
        [[50, 28, 92], [139, 92, 246]],
        [[69, 26, 43], [225, 29, 72]],
        [[15, 23, 42], [100, 116, 139]],
    ];

    public function run(): void
    {
        $this->call([
            AffiliateCategorySeeder::class,
            AffiliatePlatformSeeder::class,
        ]);

        $uploadService = app(MediaUploadService::class);

        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $defaultLocale = Language::getDefault()?->code ?? $locales[0];

        foreach ($this->products as $index => $product) {
            $categoryId = AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', $defaultLocale)
                    ->where('slug', Str::slug($product['category'])))
                ->value('id');
            $platformId = AffiliatePlatform::query()
                ->where('slug', $product['platform'])
                ->value('id');

            if ($categoryId === null || $platformId === null) {
                throw new \RuntimeException("Kategori atau platform contoh tidak tersedia untuk produk {$product['slug']}.");
            }

            $existing = AffiliateProduct::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $product['slug']))
                ->first();

            $existing ??= AffiliateProduct::query()->create([
                'affiliate_category_id' => $categoryId,
                'affiliate_platform_id' => $platformId,
                'affiliate_url' => $product['affiliate_url'],
                'price' => $product['price'],
                'original_price' => $product['original_price'],
                'is_featured' => $index < self::FEATURED_DEMO_COUNT,
                'is_active' => false,
            ]);

            foreach ($locales as $locale) {
                if ($existing->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $existing->translateOrNew($locale)->fill([
                    'title' => $product['title'],
                    'slug' => $product['slug'],
                    'description' => $product['description'],
                ])->save();
            }

            $changes = [];

            if ($existing->image_id === null) {
                [$from, $to] = $this->imagePalettes[$index % count($this->imagePalettes)];
                $imagePath = $this->generateDemoImage(
                    "affiliate-product-{$product['slug']}.jpg",
                    800,
                    800,
                    $from,
                    $to,
                );
                $changes['image_id'] = $uploadService->uploadFromPath($imagePath)->getKey();
            }

            // Hanya isi produk contoh yang belum disentuh admin sama sekali
            // agar data yang sudah dikurasi tidak tertimpa saat re-seed.
            if ($existing->price === null) {
                $changes['price'] = $product['price'];
                $changes['original_price'] = $product['original_price'];

                if ($index < self::FEATURED_DEMO_COUNT) {
                    $changes['is_featured'] = true;
                }
            }

            if ($changes !== []) {
                $existing->update($changes);
            }
        }
    }
}

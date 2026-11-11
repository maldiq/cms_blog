<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateProductSeeder extends Seeder
{
    /**
     * @var list<array{title: string, slug: string, category: string, platform: string, description: string, affiliate_url: string}>
     */
    private array $products = [
        [
            'title' => 'Contoh: Headphone Bluetooth',
            'slug' => 'contoh-headphone-bluetooth-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
        ],
        [
            'title' => 'Contoh: Charger dan Aksesori Gadget',
            'slug' => 'contoh-aksesori-gadget-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
        ],
        [
            'title' => 'Contoh: Peralatan Rumah Tangga',
            'slug' => 'contoh-peralatan-rumah-shopee',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
        ],
        [
            'title' => 'Contoh: Organizer Meja Minimalis',
            'slug' => 'contoh-organizer-meja-tiktok',
            'category' => 'Rumah Tangga',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
        ],
        [
            'title' => 'Contoh: Perlengkapan Dapur',
            'slug' => 'contoh-perlengkapan-dapur-tiktok',
            'category' => 'Dapur',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
        ],
        [
            'title' => 'Contoh: Aksesori Kecantikan',
            'slug' => 'contoh-aksesori-kecantikan-tiktok',
            'category' => 'Kecantikan',
            'platform' => 'tiktok_shop',
            'description' => 'Data contoh untuk katalog TikTok Shop. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shop.tiktok.com/',
        ],
        [
            'title' => 'Contoh: Buku dan Referensi',
            'slug' => 'contoh-buku-amazon',
            'category' => 'Buku',
            'platform' => 'amazon',
            'description' => 'Data contoh untuk katalog Amazon. Ganti dengan produk dan tautan Amazon Associates milik Anda.',
            'affiliate_url' => 'https://www.amazon.com/',
        ],
        [
            'title' => 'Contoh: Aksesori Komputer',
            'slug' => 'contoh-aksesori-komputer-amazon',
            'category' => 'Elektronik',
            'platform' => 'amazon',
            'description' => 'Data contoh untuk katalog Amazon. Ganti dengan produk dan tautan Amazon Associates milik Anda.',
            'affiliate_url' => 'https://www.amazon.com/',
        ],
        [
            'title' => 'Contoh: VPS untuk Website',
            'slug' => 'contoh-vps-hostinger',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan VPS. Ganti dengan URL kampanye affiliate VPS Anda.',
            'affiliate_url' => 'https://www.hostinger.com/vps-hosting',
        ],
        [
            'title' => 'Contoh: Cloud VPS untuk Developer',
            'slug' => 'contoh-cloud-vps-digitalocean',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan cloud VPS. Ganti dengan URL kampanye affiliate Anda.',
            'affiliate_url' => 'https://www.digitalocean.com/pricing/droplets',
        ],
        [
            'title' => 'Contoh: VPS untuk Aplikasi',
            'slug' => 'contoh-vps-vultr',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'description' => 'Data contoh layanan VPS. Ganti dengan URL kampanye affiliate Anda.',
            'affiliate_url' => 'https://www.vultr.com/pricing/',
        ],
        [
            'title' => 'Contoh: Perangkat Lunak Produktivitas',
            'slug' => 'contoh-software-produktivitas',
            'category' => 'Software',
            'platform' => 'other',
            'description' => 'Data contoh untuk program affiliate lain. Ganti dengan produk dan tautan resmi Anda.',
            'affiliate_url' => 'https://www.canva.com/',
        ],
        [
            'title' => 'Contoh: Perangkat Jaringan',
            'slug' => 'contoh-perangkat-jaringan-shopee',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
            'affiliate_url' => 'https://shopee.co.id/',
        ],
    ];

    public function run(): void
    {
        $this->call([
            AffiliateCategorySeeder::class,
            AffiliatePlatformSeeder::class,
        ]);

        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $defaultLocale = Language::getDefault()?->code ?? $locales[0];

        foreach ($this->products as $product) {
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
                'is_active' => false,
                'is_featured' => false,
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
        }
    }
}

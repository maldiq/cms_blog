<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Services\MediaUploadService;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;
use RuntimeException;

class RackNerdProductSeeder extends Seeder
{
    use GeneratesDemoImages;

    /**
     * ID affiliate RackNerd yang dipakai pada semua tautan keluar.
     */
    private const AFFILIATE_ID = '1426';

    /**
     * Data diambil dari halaman promo Black Friday RackNerd
     * (https://www.racknerd.com/BlackFriday/) pada 21 November 2026.
     * Harga asli dalam USD per tahun, dikonversi ke IDR dengan kurs
     * 16.000 IDR/USD agar tampilan locale EN kembali ke angka aslinya.
     *
     * @var array<int, array{
     *     slug: string,
     *     title: string,
     *     category: string,
     *     pid: int,
     *     price: int,
     *     featured: bool,
     *     specs: array<string, mixed>,
     *     translations: array<string, array{excerpt: string, description: string}>
     * }>
     */
    private array $products = [
        // VPS
        [
            'slug' => 'racknerd-1gb-kvm-vps',
            'title' => 'RackNerd 1 GB KVM VPS',
            'category' => 'vps',
            'pid' => 968,
            'price' => 255680,
            'featured' => false,
            'specs' => [
                'cpu_cores' => 1,
                'ram_mb' => 1024,
                'storage_gb' => 20,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 3,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => 'Berbagai lokasi',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'KVM VPS 1 vCPU, 1 GB RAM, 20 GB SSD, transfer 3 TB/bulan. Harga promo tahunan.',
                    'description' => 'Paket KVM VPS RackNerd dengan 1 vCPU, 1 GB RAM, 20 GB penyimpanan SSD, dan transfer 3 TB per bulan. Termasuk akses root penuh, 1 alamat IPv4, port jaringan 1 Gbps, panel ctrl, dan pilihan beberapa lokasi data center. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'KVM VPS with 1 vCPU, 1 GB RAM, 20 GB SSD, and 3 TB monthly transfer. Annual promo price.',
                    'description' => 'RackNerd KVM VPS plan with 1 vCPU, 1 GB RAM, 20 GB SSD storage, and 3 TB monthly transfer. Includes full root access, 1 IPv4 address, a 1 Gbps network port, the ctrl panel, and a choice of multiple data center locations. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-2-5gb-kvm-vps',
            'title' => 'RackNerd 2.5 GB KVM VPS',
            'category' => 'vps',
            'pid' => 969,
            'price' => 447680,
            'featured' => false,
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 2560,
                'storage_gb' => 30,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 6,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => 'Berbagai lokasi',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'KVM VPS 2 vCPU, 2,5 GB RAM, 30 GB SSD, transfer 6 TB/bulan. Harga promo tahunan.',
                    'description' => 'Paket KVM VPS RackNerd dengan 2 vCPU, 2,5 GB RAM, 30 GB penyimpanan SSD, dan transfer 6 TB per bulan. Termasuk akses root penuh, 1 alamat IPv4, port jaringan 1 Gbps, panel ctrl, dan pilihan beberapa lokasi data center. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'KVM VPS with 2 vCPU, 2.5 GB RAM, 30 GB SSD, and 6 TB monthly transfer. Annual promo price.',
                    'description' => 'RackNerd KVM VPS plan with 2 vCPU, 2.5 GB RAM, 30 GB SSD storage, and 6 TB monthly transfer. Includes full root access, 1 IPv4 address, a 1 Gbps network port, the ctrl panel, and a choice of multiple data center locations. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-4gb-kvm-vps',
            'title' => 'RackNerd 4 GB KVM VPS',
            'category' => 'vps',
            'pid' => 970,
            'price' => 767680,
            'featured' => true,
            'specs' => [
                'cpu_cores' => 4,
                'ram_mb' => 4096,
                'storage_gb' => 60,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 10,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => 'Berbagai lokasi',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'KVM VPS 4 vCPU, 4 GB RAM, 60 GB SSD, transfer 10 TB/bulan. Paket paling populer.',
                    'description' => 'Paket KVM VPS RackNerd yang ditandai paling populer: 4 vCPU, 4 GB RAM, 60 GB penyimpanan SSD, dan transfer 10 TB per bulan. Termasuk akses root penuh, 1 alamat IPv4, port jaringan 1 Gbps, panel ctrl, dan pilihan beberapa lokasi data center. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'KVM VPS with 4 vCPU, 4 GB RAM, 60 GB SSD, and 10 TB monthly transfer. Most popular plan.',
                    'description' => 'RackNerd KVM VPS marked as the most popular choice: 4 vCPU, 4 GB RAM, 60 GB SSD storage, and 10 TB monthly transfer. Includes full root access, 1 IPv4 address, a 1 Gbps network port, the ctrl panel, and a choice of multiple data center locations. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-6gb-kvm-vps',
            'title' => 'RackNerd 6 GB KVM VPS',
            'category' => 'vps',
            'pid' => 971,
            'price' => 1199680,
            'featured' => false,
            'specs' => [
                'cpu_cores' => 6,
                'ram_mb' => 6144,
                'storage_gb' => 85,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 15,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => 'Berbagai lokasi',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'KVM VPS 6 vCPU, 6 GB RAM, 85 GB SSD, transfer 15 TB/bulan. Harga promo tahunan.',
                    'description' => 'Paket KVM VPS RackNerd dengan 6 vCPU, 6 GB RAM, 85 GB penyimpanan SSD, dan transfer 15 TB per bulan. Termasuk akses root penuh, 1 alamat IPv4, port jaringan 1 Gbps, panel ctrl, dan pilihan beberapa lokasi data center. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'KVM VPS with 6 vCPU, 6 GB RAM, 85 GB SSD, and 15 TB monthly transfer. Annual promo price.',
                    'description' => 'RackNerd KVM VPS plan with 6 vCPU, 6 GB RAM, 85 GB SSD storage, and 15 TB monthly transfer. Includes full root access, 1 IPv4 address, a 1 Gbps network port, the ctrl panel, and a choice of multiple data center locations. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-8gb-kvm-vps',
            'title' => 'RackNerd 8 GB KVM VPS',
            'category' => 'vps',
            'pid' => 972,
            'price' => 1519680,
            'featured' => false,
            'specs' => [
                'cpu_cores' => 6,
                'ram_mb' => 8192,
                'storage_gb' => 140,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 20,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => 'Berbagai lokasi',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'KVM VPS 6 vCPU, 8 GB RAM, 140 GB SSD, transfer 20 TB/bulan. Harga promo tahunan.',
                    'description' => 'Paket KVM VPS RackNerd dengan 6 vCPU, 8 GB RAM, 140 GB penyimpanan SSD, dan transfer 20 TB per bulan. Termasuk akses root penuh, 1 alamat IPv4, port jaringan 1 Gbps, panel ctrl, dan pilihan beberapa lokasi data center. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'KVM VPS with 6 vCPU, 8 GB RAM, 140 GB SSD, and 20 TB monthly transfer. Annual promo price.',
                    'description' => 'RackNerd KVM VPS plan with 6 vCPU, 8 GB RAM, 140 GB SSD storage, and 20 TB monthly transfer. Includes full root access, 1 IPv4 address, a 1 Gbps network port, the ctrl panel, and a choice of multiple data center locations. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],

        // Shared hosting
        [
            'slug' => 'racknerd-shared-hosting-35gb',
            'title' => 'RackNerd Shared Hosting 35 GB',
            'category' => 'shared-hosting',
            'pid' => 977,
            'price' => 151840,
            'featured' => false,
            'specs' => [
                'storage_gb' => 35,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 5,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 35 GB SSD untuk 5 domain, transfer 5 TB/bulan, backup harian, dan SSL gratis.',
                    'description' => 'Paket shared hosting RackNerd dengan 35 GB penyimpanan SSD dan transfer 5 TB per bulan untuk 5 domain. Termasuk migrasi website gratis, backup harian, SSL gratis, LiteSpeed, Softaculous, CloudLinux, dan panel AdminBolt. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 35 GB SSD for 5 domains, 5 TB monthly transfer, daily backups, and free SSL.',
                    'description' => 'RackNerd shared hosting plan with 35 GB SSD storage and 5 TB monthly transfer for 5 domains. Includes free website migration, daily backups, free SSL, LiteSpeed, Softaculous, CloudLinux, and the AdminBolt panel. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-shared-hosting-90gb',
            'title' => 'RackNerd Shared Hosting 90 GB',
            'category' => 'shared-hosting',
            'pid' => 978,
            'price' => 351840,
            'featured' => true,
            'specs' => [
                'storage_gb' => 90,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 15,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 90 GB SSD, domain tak terbatas, transfer 15 TB/bulan, backup harian, SSL gratis.',
                    'description' => 'Paket shared hosting RackNerd dengan 90 GB penyimpanan SSD dan transfer 15 TB per bulan untuk domain tak terbatas. Termasuk migrasi website gratis, backup harian, SSL gratis, LiteSpeed, Softaculous, CloudLinux, dan panel AdminBolt. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 90 GB SSD, unlimited domains, 15 TB monthly transfer, daily backups, and free SSL.',
                    'description' => 'RackNerd shared hosting plan with 90 GB SSD storage and 15 TB monthly transfer for unlimited domains. Includes free website migration, daily backups, free SSL, LiteSpeed, Softaculous, CloudLinux, and the AdminBolt panel. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-shared-hosting-200gb',
            'title' => 'RackNerd Shared Hosting 200 GB',
            'category' => 'shared-hosting',
            'pid' => 979,
            'price' => 798080,
            'featured' => false,
            'specs' => [
                'storage_gb' => 200,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 40,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 200 GB SSD, domain tak terbatas, transfer 40 TB/bulan, backup harian, SSL gratis.',
                    'description' => 'Paket shared hosting RackNerd dengan 200 GB penyimpanan SSD dan transfer 40 TB per bulan untuk domain tak terbatas. Termasuk migrasi website gratis, backup harian, SSL gratis, LiteSpeed, Softaculous, CloudLinux, dan panel AdminBolt. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 200 GB SSD, unlimited domains, 40 TB monthly transfer, daily backups, and free SSL.',
                    'description' => 'RackNerd shared hosting plan with 200 GB SSD storage and 40 TB monthly transfer for unlimited domains. Includes free website migration, daily backups, free SSL, LiteSpeed, Softaculous, CloudLinux, and the AdminBolt panel. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],

        // Reseller hosting
        [
            'slug' => 'racknerd-reseller-hosting-80gb',
            'title' => 'RackNerd Reseller Hosting 80 GB',
            'category' => 'reseller-hosting',
            'pid' => 980,
            'price' => 478080,
            'featured' => false,
            'specs' => [
                'storage_gb' => 80,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 5,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Reseller hosting 80 GB SSD untuk 15 akun, transfer 5 TB/bulan, panel white-label, Clientexec gratis.',
                    'description' => 'Paket reseller hosting RackNerd dengan 80 GB penyimpanan SSD dan transfer 5 TB per bulan untuk 15 akun hosting. Termasuk panel reseller AdminBolt, white-label hosting, migrasi akun gratis, SSL gratis, backup harian, LiteSpeed, CloudLinux, dan lisensi Clientexec gratis. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Reseller hosting with 80 GB SSD for 15 accounts, 5 TB monthly transfer, white-label panel, free Clientexec.',
                    'description' => 'RackNerd reseller hosting plan with 80 GB SSD storage and 5 TB monthly transfer for 15 hosting accounts. Includes the AdminBolt reseller panel, white-label hosting, free account migrations, free SSL, daily backups, LiteSpeed, CloudLinux, and a free Clientexec license. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-reseller-hosting-115gb',
            'title' => 'RackNerd Reseller Hosting 115 GB',
            'category' => 'reseller-hosting',
            'pid' => 981,
            'price' => 878080,
            'featured' => true,
            'specs' => [
                'storage_gb' => 115,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 20,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Reseller hosting 115 GB SSD untuk 25 akun, transfer 20 TB/bulan, panel white-label, Clientexec gratis.',
                    'description' => 'Paket reseller hosting RackNerd dengan 115 GB penyimpanan SSD dan transfer 20 TB per bulan untuk 25 akun hosting. Termasuk panel reseller AdminBolt, white-label hosting, migrasi akun gratis, SSL gratis, backup harian, LiteSpeed, CloudLinux, dan lisensi Clientexec gratis. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Reseller hosting with 115 GB SSD for 25 accounts, 20 TB monthly transfer, white-label panel, free Clientexec.',
                    'description' => 'RackNerd reseller hosting plan with 115 GB SSD storage and 20 TB monthly transfer for 25 hosting accounts. Includes the AdminBolt reseller panel, white-label hosting, free account migrations, free SSL, daily backups, LiteSpeed, CloudLinux, and a free Clientexec license. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
        [
            'slug' => 'racknerd-reseller-hosting-185gb',
            'title' => 'RackNerd Reseller Hosting 185 GB',
            'category' => 'reseller-hosting',
            'pid' => 982,
            'price' => 1358080,
            'featured' => false,
            'specs' => [
                'storage_gb' => 185,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 30,
                'location' => 'Dallas, USA',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Reseller hosting 185 GB SSD untuk 50 akun, transfer 30 TB/bulan, panel white-label, Clientexec gratis.',
                    'description' => 'Paket reseller hosting RackNerd dengan 185 GB penyimpanan SSD dan transfer 30 TB per bulan untuk 50 akun hosting. Termasuk panel reseller AdminBolt, white-label hosting, migrasi akun gratis, SSL gratis, backup harian, LiteSpeed, CloudLinux, dan lisensi Clientexec gratis. Lokasi data center Dallas, AS. Harga promo Black Friday ditagih tahunan di muka.',
                ],
                'en' => [
                    'excerpt' => 'Reseller hosting with 185 GB SSD for 50 accounts, 30 TB monthly transfer, white-label panel, free Clientexec.',
                    'description' => 'RackNerd reseller hosting plan with 185 GB SSD storage and 30 TB monthly transfer for 50 hosting accounts. Includes the AdminBolt reseller panel, white-label hosting, free account migrations, free SSL, daily backups, LiteSpeed, CloudLinux, and a free Clientexec license. Dallas, USA data center location. Black Friday promo pricing is billed annually upfront.',
                ],
            ],
        ],
    ];

    /**
     * @var list<array{0: array{0: int, 1: int, 2: int}, 1: array{0: int, 1: int, 2: int}}>
     */
    private array $imagePalettes = [
        [[11, 95, 176], [3, 58, 128]],
        [[8, 126, 212], [13, 74, 140]],
        [[25, 118, 210], [4, 47, 104]],
        [[6, 84, 158], [2, 38, 84]],
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $platform = $this->platform($locales);
        $categories = $this->categories();

        foreach ($this->products as $index => $data) {
            $category = $categories[$data['category']] ?? null;

            if ($category === null) {
                throw new RuntimeException("Kategori affiliate [{$data['category']}] belum ada. Jalankan AffiliateCategorySeeder lebih dulu.");
            }

            $product = AffiliateProduct::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $data['slug']))
                ->first();

            if ($product === null) {
                $product = new AffiliateProduct;
                $product->fill([
                    'affiliate_category_id' => $category->getKey(),
                    'affiliate_platform_id' => $platform->getKey(),
                    'is_hosting' => true,
                    'affiliate_url' => $this->orderUrl($data['pid']),
                    'price' => $data['price'],
                    'original_price' => null,
                    'billing_period' => 'yearly',
                    'is_featured' => $data['featured'],
                    'is_active' => true,
                    'last_checked_at' => now(),
                ] + $data['specs'])->save();
            } else {
                $changes = [];

                if ($product->affiliate_category_id === null) {
                    $changes['affiliate_category_id'] = $category->getKey();
                }

                if ($product->affiliate_platform_id === null) {
                    $changes['affiliate_platform_id'] = $platform->getKey();
                }

                if (! $product->is_hosting) {
                    $changes['is_hosting'] = true;
                }

                if (blank($product->affiliate_url)) {
                    $changes['affiliate_url'] = $this->orderUrl($data['pid']);
                }

                if ($product->price === null) {
                    $changes['price'] = $data['price'];
                    $changes['billing_period'] = 'yearly';
                }

                if (! $product->hasSpecs()) {
                    $changes += $data['specs'];
                    $changes['last_checked_at'] = now();
                }

                if ($changes !== []) {
                    $product->update($changes);
                }
            }

            foreach ($locales as $locale) {
                $content = $data['translations'][$locale] ?? $data['translations']['id'];
                $translation = $product->translate($locale, false);

                if ($translation === null) {
                    $product->translateOrNew($locale)->fill([
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'excerpt' => $content['excerpt'],
                        'description' => $content['description'],
                    ])->save();

                    continue;
                }

                $dirty = [];

                if (blank($translation->title)) {
                    $dirty['title'] = $data['title'];
                }

                if (blank($translation->slug)) {
                    $dirty['slug'] = $data['slug'];
                }

                if (blank($translation->excerpt)) {
                    $dirty['excerpt'] = $content['excerpt'];
                }

                if (blank($translation->description)) {
                    $dirty['description'] = $content['description'];
                }

                if ($dirty !== []) {
                    $translation->fill($dirty)->save();
                }
            }

            if (config('affiliate.seed_demo_images', true) && $product->image_id === null) {
                [$from, $to] = $this->imagePalettes[$index % count($this->imagePalettes)];
                $imagePath = $this->generateDemoImage(
                    "affiliate-product-{$data['slug']}.jpg",
                    800,
                    800,
                    $from,
                    $to,
                );

                $product->update([
                    'image_id' => app(MediaUploadService::class)->uploadFromPath($imagePath)->getKey(),
                ]);
            }
        }
    }

    private function orderUrl(int $pid): string
    {
        return 'https://my.racknerd.com/cart.php?a=add&pid='.$pid.'&aff='.self::AFFILIATE_ID;
    }

    /**
     * @param  list<string>  $locales
     */
    private function platform(array $locales): AffiliatePlatform
    {
        $platform = AffiliatePlatform::query()->firstOrCreate(
            ['slug' => 'racknerd'],
            [
                'is_active' => true,
                'sort_order' => 10,
            ],
        );

        foreach ($locales as $locale) {
            if ($platform->translations()->where('locale', $locale)->exists()) {
                continue;
            }

            $platform->translateOrNew($locale)->fill(['name' => 'RackNerd'])->save();
        }

        return $platform;
    }

    /**
     * @return array<string, AffiliateCategory>
     */
    private function categories(): array
    {
        $slugs = array_unique(array_column($this->products, 'category'));
        $categories = [];

        foreach ($slugs as $slug) {
            $category = AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $slug))
                ->first();

            if ($category !== null) {
                $categories[$slug] = $category;
            }
        }

        return $categories;
    }
}

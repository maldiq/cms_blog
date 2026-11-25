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

class HostHatchProductSeeder extends Seeder
{
    use GeneratesDemoImages;

    /**
     * Paket promo anniversary HostHatch. Harga dalam USD dikonversi ke IDR
     * memakai kurs 16.000 IDR/USD di config affiliate, sehingga locale EN
     * kembali menampilkan angka USD aslinya.
     *
     * Sumber data: https://lowendtalk.com/discussion/216524/calmly-celebrating-15-years-in-business
     * (dipublikasikan 22 April 2026, promo berakhir 23 April 2026 pukul
     * 11:00 CET). Stok sangat terbatas dan semua penjualan final, sehingga
     * ketersediaan wajib dicek ulang di halaman order.
     *
     * @var array<int, array{
     *     slug: string,
     *     title: string,
     *     url: string,
     *     price: int,
     *     featured?: bool,
     *     billing_period?: string,
     *     specs: array<string, mixed>,
     *     translations: array<string, array{excerpt: string, description: string}>
     * }>
     */
    private array $products = [
        [
            'slug' => 'hosthatch-nvme-4gb',
            'title' => 'HostHatch NVMe 4 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 560000,
            'featured' => true,
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 4096,
                'storage_gb' => 50,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 5,
                'ipv6_support' => true,
                'location' => 'EU, US & APAC (10 locations)',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS NVMe 2 core, 4 GB RAM, 50 GB NVMe, bandwidth 5 TB. Promo anniversary HostHatch, USD 35/tahun.',
                    'description' => 'Paket VPS NVMe HostHatch dengan 2 CPU core (0.5 dedicated core dan 1.5 fairly shared core) berbasis AMD EPYC ROME/MILAN, 4 GB RAM, 50 GB penyimpanan NVMe, dan bandwidth 5 TB (3 TB di APAC, 1.5 TB di Seoul). Tersedia di London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, dan Seoul. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 35 per tahun; pembayaran 3 tahun USD 95 dengan RAM, penyimpanan, dan bandwidth 2x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'NVMe VPS with 2 cores, 4 GB RAM, 50 GB NVMe, and 5 TB bandwidth. HostHatch anniversary promo, USD 35/year.',
                    'description' => 'HostHatch NVMe VPS plan with 2 CPU cores (0.5 dedicated and 1.5 fairly shared cores) on AMD EPYC ROME/MILAN, 4 GB RAM, 50 GB NVMe storage, and 5 TB bandwidth (3 TB in APAC, 1.5 TB in Seoul). Available in London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, and Seoul. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 35 per year; the 3-year payment is USD 95 with double RAM, storage, and bandwidth. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-nvme-8gb',
            'title' => 'HostHatch NVMe 8 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 1040000,
            'specs' => [
                'cpu_cores' => 4,
                'ram_mb' => 8192,
                'storage_gb' => 100,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 10,
                'ipv6_support' => true,
                'location' => 'EU, US & APAC (10 locations)',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS NVMe 4 core, 8 GB RAM, 100 GB NVMe, bandwidth 10 TB. Promo anniversary HostHatch, USD 65/tahun.',
                    'description' => 'Paket VPS NVMe HostHatch dengan 4 CPU core (1 dedicated core dan 3 fairly shared core) berbasis AMD EPYC ROME/MILAN, 8 GB RAM, 100 GB penyimpanan NVMe, dan bandwidth 10 TB (6 TB di APAC, 3 TB di Seoul). Tersedia di London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, dan Seoul. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 65 per tahun; pembayaran 3 tahun USD 170 dengan RAM, penyimpanan, dan bandwidth 2x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'NVMe VPS with 4 cores, 8 GB RAM, 100 GB NVMe, and 10 TB bandwidth. HostHatch anniversary promo, USD 65/year.',
                    'description' => 'HostHatch NVMe VPS plan with 4 CPU cores (1 dedicated and 3 fairly shared cores) on AMD EPYC ROME/MILAN, 8 GB RAM, 100 GB NVMe storage, and 10 TB bandwidth (6 TB in APAC, 3 TB in Seoul). Available in London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, and Seoul. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 65 per year; the 3-year payment is USD 170 with double RAM, storage, and bandwidth. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-nvme-16gb',
            'title' => 'HostHatch NVMe 16 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 2080000,
            'specs' => [
                'cpu_cores' => 4,
                'ram_mb' => 16384,
                'storage_gb' => 200,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 25,
                'ipv6_support' => true,
                'location' => 'EU, US & APAC (10 locations)',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS NVMe 4 core, 16 GB RAM, 200 GB NVMe, bandwidth 25 TB. Promo anniversary HostHatch, USD 130/tahun.',
                    'description' => 'Paket VPS NVMe HostHatch dengan 4 CPU core (2 dedicated core dan 2 fairly shared core) berbasis AMD EPYC ROME/MILAN, 16 GB RAM, 200 GB penyimpanan NVMe, dan bandwidth 25 TB (12 TB di APAC, 6 TB di Seoul). Tersedia di London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, dan Seoul. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 130 per tahun; pembayaran 3 tahun USD 350 dengan RAM, penyimpanan, dan bandwidth 2x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'NVMe VPS with 4 cores, 16 GB RAM, 200 GB NVMe, and 25 TB bandwidth. HostHatch anniversary promo, USD 130/year.',
                    'description' => 'HostHatch NVMe VPS plan with 4 CPU cores (2 dedicated and 2 fairly shared cores) on AMD EPYC ROME/MILAN, 16 GB RAM, 200 GB NVMe storage, and 25 TB bandwidth (12 TB in APAC, 6 TB in Seoul). Available in London, Amsterdam, Chicago, Hong Kong, Los Angeles, New York, Singapore, Sydney, Tokyo, and Seoul. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 130 per year; the 3-year payment is USD 350 with double RAM, storage, and bandwidth. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-hfc-4gb',
            'title' => 'HostHatch HFC 4 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 1040000,
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 4096,
                'storage_gb' => 40,
                'storage_type' => 'PCIe Gen5 NVMe',
                'bandwidth_tb' => 5,
                'ipv6_support' => true,
                'location' => 'EU, US & APAC (9 locations)',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'HFC VPS 2 vCPU 5,7 GHz, 4 GB RAM, 40 GB PCIe Gen5 NVMe, bandwidth 5 TB. USD 65/tahun.',
                    'description' => 'Paket HostHatch High Frequency Compute dengan 2 vCPU berkecepatan 5,7 GHz boosted (fair shared), 4 GB RAM, 40 GB PCIe Gen5 NVMe, dan bandwidth 5 TB (2 TB di APAC, 1 TB di Seoul). Tersedia di London, Amsterdam, Chicago, Los Angeles, New York, Seoul, Singapore, Sydney, dan Tokyo. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 65 per tahun; pembayaran 2 tahun USD 120 dengan penyimpanan dan bandwidth 2x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'HFC VPS with 2 vCPUs at 5.7 GHz, 4 GB RAM, 40 GB PCIe Gen5 NVMe, and 5 TB bandwidth. USD 65/year.',
                    'description' => 'HostHatch High Frequency Compute plan with 2 vCPUs boosted to 5.7 GHz (fair shared), 4 GB RAM, 40 GB PCIe Gen5 NVMe, and 5 TB bandwidth (2 TB in APAC, 1 TB in Seoul). Available in London, Amsterdam, Chicago, Los Angeles, New York, Seoul, Singapore, Sydney, and Tokyo. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 65 per year; the 2-year payment is USD 120 with double storage and bandwidth. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-hfc-12gb',
            'title' => 'HostHatch HFC 12 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 2240000,
            'specs' => [
                'cpu_cores' => 3,
                'ram_mb' => 12288,
                'storage_gb' => 100,
                'storage_type' => 'PCIe Gen5 NVMe',
                'bandwidth_tb' => 10,
                'ipv6_support' => true,
                'location' => 'EU, US & APAC (9 locations)',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'HFC VPS 3 vCPU 5,7 GHz, 12 GB RAM, 100 GB PCIe Gen5 NVMe, bandwidth 10 TB. USD 140/tahun.',
                    'description' => 'Paket HostHatch High Frequency Compute dengan 3 vCPU berkecepatan 5,7 GHz boosted (1 dedicated core dan 2 shared core), 12 GB RAM, 100 GB PCIe Gen5 NVMe, dan bandwidth 10 TB (5 TB di APAC, 2 TB di Seoul). Tersedia di London, Amsterdam, Chicago, Los Angeles, New York, Seoul, Singapore, Sydney, dan Tokyo. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 140 per tahun; pembayaran 2 tahun USD 270 dengan penyimpanan dan bandwidth 2x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'HFC VPS with 3 vCPUs at 5.7 GHz, 12 GB RAM, 100 GB PCIe Gen5 NVMe, and 10 TB bandwidth. USD 140/year.',
                    'description' => 'HostHatch High Frequency Compute plan with 3 vCPUs boosted to 5.7 GHz (1 dedicated and 2 shared cores), 12 GB RAM, 100 GB PCIe Gen5 NVMe, and 10 TB bandwidth (5 TB in APAC, 2 TB in Seoul). Available in London, Amsterdam, Chicago, Los Angeles, New York, Seoul, Singapore, Sydney, and Tokyo. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 140 per year; the 2-year payment is USD 270 with double storage and bandwidth. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-storage-2gb',
            'title' => 'HostHatch Storage 2 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 2080000,
            'billing_period' => 'triennial',
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 2048,
                'storage_gb' => 2000,
                'storage_type' => 'HDD RAID-10',
                'bandwidth_tb' => 10,
                'ipv6_support' => true,
                'location' => 'Chicago, Los Angeles, Singapore, Sydney, Hong Kong',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Storage VPS 2 core, 2 GB RAM, 2 TB HDD RAID-10 + 20 GB NVMe, bandwidth 10 TB. USD 130/3 tahun.',
                    'description' => 'Paket HostHatch Storage dengan 2 CPU core, 2 GB RAM, 20 GB NVMe untuk sistem operasi, 2 TB HDD RAID-10, dan bandwidth 10 TB (3 TB di APAC). Tersedia di Chicago, Los Angeles, Singapore, Sydney, dan Hong Kong. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 130 untuk 3 tahun; paket ini tidak memiliki opsi tahunan. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'Storage VPS with 2 cores, 2 GB RAM, 2 TB RAID-10 HDD plus 20 GB NVMe, and 10 TB bandwidth. USD 130/3 years.',
                    'description' => 'HostHatch Storage plan with 2 CPU cores, 2 GB RAM, 20 GB NVMe for the operating system, 2 TB RAID-10 HDD, and 10 TB bandwidth (3 TB in APAC). Available in Chicago, Los Angeles, Singapore, Sydney, and Hong Kong. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 130 for 3 years; this plan has no annual option. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
        [
            'slug' => 'hosthatch-storage-4gb',
            'title' => 'HostHatch Storage 4 GB',
            'url' => 'https://cloud.hosthatch.com/a/5864',
            'price' => 2240000,
            'specs' => [
                'cpu_cores' => 4,
                'ram_mb' => 4096,
                'storage_gb' => 5000,
                'storage_type' => 'HDD RAID-10',
                'bandwidth_tb' => 35,
                'ipv6_support' => true,
                'location' => 'Chicago, Los Angeles, Singapore, Sydney, Hong Kong',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Storage VPS 4 core, 4 GB RAM, 5 TB HDD RAID-10 + 40 GB NVMe, bandwidth 35 TB. USD 140/tahun.',
                    'description' => 'Paket HostHatch Storage dengan 4 CPU core, 4 GB RAM, 40 GB NVMe untuk sistem operasi, 5 TB HDD RAID-10, dan bandwidth 35 TB (10 TB di APAC). Tersedia di Chicago, Los Angeles, Singapore, Sydney, dan Hong Kong. IPv6 /64 dapat diaktifkan dari panel. Harga promo anniversary USD 140 per tahun; pembayaran 3 tahun USD 400 dengan RAM 1,5x, bandwidth 2x, dan penyimpanan 1,5x. Stok sangat terbatas, semua penjualan final tanpa refund atau tukar paket, dan setup hingga 10 hari kerja.',
                ],
                'en' => [
                    'excerpt' => 'Storage VPS with 4 cores, 4 GB RAM, 5 TB RAID-10 HDD plus 40 GB NVMe, and 35 TB bandwidth. USD 140/year.',
                    'description' => 'HostHatch Storage plan with 4 CPU cores, 4 GB RAM, 40 GB NVMe for the operating system, 5 TB RAID-10 HDD, and 35 TB bandwidth (10 TB in APAC). Available in Chicago, Los Angeles, Singapore, Sydney, and Hong Kong. IPv6 /64 can be enabled from the control panel. Anniversary promo price is USD 140 per year; the 3-year payment is USD 400 with 1.5x RAM, 2x bandwidth, and 1.5x storage. Stock is extremely limited, all sales are final with no refunds or plan changes, and setup takes up to 10 business days.',
                ],
            ],
        ],
    ];

    /**
     * @var list<array{0: int, 1: int, 2: int}>
     */
    private array $imagePalettes = [
        [[194, 65, 12], [251, 146, 60]],
        [[154, 52, 18], [253, 186, 116]],
        [[120, 53, 15], [245, 158, 11]],
        [[124, 45, 18], [251, 191, 36]],
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'vps'))
            ->first();

        if ($category === null) {
            throw new RuntimeException('Kategori affiliate [vps] belum ada. Jalankan AffiliateCategorySeeder lebih dulu.');
        }

        $platform = AffiliatePlatform::query()->firstOrCreate(
            ['slug' => 'hosthatch'],
            ['is_active' => true, 'sort_order' => 15],
        );

        foreach ($locales as $locale) {
            if ($platform->translations()->where('locale', $locale)->exists()) {
                continue;
            }

            $platform->translateOrNew($locale)->fill(['name' => 'HostHatch'])->save();
        }

        foreach ($this->products as $index => $data) {
            $product = AffiliateProduct::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $data['slug']))
                ->first();

            if ($product === null) {
                $product = new AffiliateProduct;
                $product->fill([
                    'affiliate_category_id' => $category->getKey(),
                    'affiliate_platform_id' => $platform->getKey(),
                    'is_hosting' => true,
                    'affiliate_url' => $data['url'],
                    'price' => $data['price'],
                    'original_price' => null,
                    'billing_period' => $data['billing_period'] ?? 'yearly',
                    'is_featured' => $data['featured'] ?? false,
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
                    $changes['affiliate_url'] = $data['url'];
                }

                if ($product->price === null) {
                    $changes['price'] = $data['price'];
                    $changes['billing_period'] = $data['billing_period'] ?? 'yearly';
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
}

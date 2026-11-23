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

class PartnerHostingProductSeeder extends Seeder
{
    use GeneratesDemoImages;

    /**
     * Paket terlaris dari empat penyedia hosting. Harga asli dalam USD
     * dikonversi ke IDR memakai kurs 16.000 IDR/USD di config affiliate,
     * sehingga locale EN kembali menampilkan angka USD aslinya.
     *
     * Sumber data (diambil 23 November 2026):
     * - DomaiNesia: https://www.domainesia.com/hosting/ (harga rupiah)
     * - Vultr: halaman pricing resmi, snapshot arsip 17 Oktober 2026
     *   karena situs menolak akses bot (paket High Performance AMD)
     * - HostBrr: order form cPanel 25 GB dan cPanel Reseller 50 GB
     * - NameCrane: cPanel shared, cPanel reseller, DirectAdmin reseller
     *
     * @var array<int, array{
     *     platform: string,
     *     category: string,
     *     slug: string,
     *     title: string,
     *     url: string,
     *     price: int,
     *     original_price?: int,
     *     featured?: bool,
     *     specs: array<string, mixed>,
     *     translations: array<string, array{excerpt: string, description: string}>
     * }>
     */
    private array $products = [
        // DomaiNesia — shared hosting
        [
            'platform' => 'domainesia',
            'category' => 'shared-hosting',
            'slug' => 'domainesia-nimbus-one',
            'title' => 'DomaiNesia Nimbus One',
            'url' => 'https://my.domainesia.com/ref.php?u=27743',
            'price' => 18000,
            'original_price' => 30000,
            'specs' => [
                'storage_gb' => 2,
                'storage_type' => 'SSD NVMe',
                'ipv6_support' => true,
                'location' => 'Jakarta & Singapura',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 2 GB SSD NVMe untuk 1 website, bandwidth unlimited, dan domain .ONLINE gratis. Rp18.000/bulan.',
                    'description' => 'Paket shared hosting DomaiNesia Nimbus One memberi 2 GB penyimpanan SSD NVMe untuk 1 website, 2 database, dan 3 email. Bandwidth unlimited, remote backup ke data center berbeda lokasi, serta pilihan server Jakarta atau Singapura. Batas resource per akun: CPU 75%, RAM 512 MB, dan 8 entry process. Termasuk cPanel, CloudLinux, HTTP/3 QUIC, PHP 5.x–8.x, MySQL/PostgreSQL, WordPress Manager, SSL Let\'s Encrypt, 400+ apps installer, Imunify360, IPv4 & IPv6, dan domain .ONLINE gratis untuk pembelian hosting minimal 1 tahun.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 2 GB NVMe SSD for 1 website, unlimited bandwidth, and a free .ONLINE domain. USD 1.13/month.',
                    'description' => 'DomaiNesia Nimbus One shared hosting provides 2 GB of NVMe SSD storage for 1 website, 2 databases, and 3 email accounts. Bandwidth is unlimited, with remote backups to a data center in a different location and a choice of Jakarta or Singapore servers. Per-account limits are 75% CPU, 512 MB RAM, and 8 entry processes. Includes cPanel, CloudLinux, HTTP/3 QUIC, PHP 5.x–8.x, MySQL/PostgreSQL, WordPress Manager, Let\'s Encrypt SSL, 380+ app installer, Imunify360, IPv4 and IPv6, and a free .ONLINE domain with hosting purchases of at least one year.',
                ],
            ],
        ],
        [
            'platform' => 'domainesia',
            'category' => 'shared-hosting',
            'slug' => 'domainesia-nimbus-go',
            'title' => 'DomaiNesia Nimbus Go',
            'url' => 'https://my.domainesia.com/ref.php?u=27743',
            'price' => 32000,
            'original_price' => 65000,
            'featured' => true,
            'specs' => [
                'storage_gb' => 15,
                'storage_type' => 'SSD NVMe',
                'ipv6_support' => true,
                'location' => 'Jakarta & Singapura',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 15 GB SSD NVMe, website/database/email unlimited, domain .COM gratis. Paket populer DomaiNesia, Rp32.000/bulan.',
                    'description' => 'Paket shared hosting DomaiNesia Nimbus Go yang ditandai populer memberi 15 GB SSD NVMe dengan website, database, dan email tak terbatas. Bandwidth unlimited, remote backup, dan pilihan server Jakarta atau Singapura. Batas resource per akun: CPU 100%, RAM 1 GB, dan 12 entry process, dengan 2x replikasi data. Termasuk cPanel, CloudLinux, HTTP/3 QUIC, Node.js, Redis, Memcached, SSH, Git, WordPress Accelerator, SSL gratis, Imunify360, IPv4 & IPv6, serta domain .COM gratis untuk pembelian hosting minimal 1 tahun.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 15 GB NVMe SSD, unlimited websites/databases/email, and a free .COM domain. DomaiNesia popular plan, USD 2/month.',
                    'description' => 'DomaiNesia Nimbus Go, marked as the popular plan, provides 15 GB of NVMe SSD storage with unlimited websites, databases, and email accounts. Bandwidth is unlimited, with remote backups and a choice of Jakarta or Singapore servers. Per-account limits are 100% CPU, 1 GB RAM, and 12 entry processes, with 2x data replication. Includes cPanel, CloudLinux, HTTP/3 QUIC, Node.js, Redis, Memcached, SSH, Git, WordPress Accelerator, free SSL, Imunify360, IPv4 and IPv6, plus a free .COM domain with hosting purchases of at least one year.',
                ],
            ],
        ],
        [
            'platform' => 'domainesia',
            'category' => 'shared-hosting',
            'slug' => 'domainesia-nimbus-plus',
            'title' => 'DomaiNesia Nimbus Plus',
            'url' => 'https://my.domainesia.com/ref.php?u=27743',
            'price' => 59000,
            'original_price' => 120000,
            'specs' => [
                'storage_gb' => 25,
                'storage_type' => 'SSD NVMe',
                'ipv6_support' => true,
                'location' => 'Jakarta & Singapura',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'Shared hosting 25 GB SSD NVMe untuk website trafik tinggi, resource 2x Nimbus Go, domain .COM gratis. Rp59.000/bulan.',
                    'description' => 'Paket shared hosting DomaiNesia Nimbus Plus memberi 25 GB SSD NVMe dengan website, database, dan email tak terbatas. Dirancang untuk website trafik tinggi, Elementor, dan WooCommerce. Batas resource per akun: CPU 200%, RAM 2 GB, dan 20 entry process, dengan 4x performa dan 2x replikasi data. Tersedia server Jakarta atau Singapura, bandwidth unlimited, remote backup, cPanel, CloudLinux, Redis, Memcached, SSH, Git, Python, MongoDB, Ruby, Go, Rust, SSL gratis, Imunify360, IPv4 & IPv6, serta domain .COM gratis untuk pembelian hosting minimal 1 tahun.',
                ],
                'en' => [
                    'excerpt' => 'Shared hosting with 25 GB NVMe SSD for high-traffic sites, twice the resources of Nimbus Go, and a free .COM domain. USD 3.69/month.',
                    'description' => 'DomaiNesia Nimbus Plus provides 25 GB of NVMe SSD storage with unlimited websites, databases, and email accounts. It is built for high-traffic sites, Elementor, and WooCommerce. Per-account limits are 200% CPU, 2 GB RAM, and 20 entry processes, with 4x performance and 2x data replication. Choose a Jakarta or Singapore server, with unlimited bandwidth, remote backups, cPanel, CloudLinux, Redis, Memcached, SSH, Git, Python, MongoDB, Ruby, Go, Rust, free SSL, Imunify360, IPv4 and IPv6, plus a free .COM domain with hosting purchases of at least one year.',
                ],
            ],
        ],

        // Vultr — VPS
        [
            'platform' => 'vultr',
            'category' => 'vps',
            'slug' => 'vultr-cloud-compute-1gb',
            'title' => 'Vultr Cloud Compute 1 GB',
            'url' => 'https://www.vultr.com/?ref=7309768',
            'price' => 96000,
            'specs' => [
                'cpu_cores' => 1,
                'ram_mb' => 1024,
                'storage_gb' => 25,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 2,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'location' => '33 region data center',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS 1 vCPU, 1 GB RAM, 25 GB NVMe, transfer 2 TB/bulan. High Performance AMD, tagihan bulanan.',
                    'description' => 'Paket Vultr Cloud Compute High Performance AMD dengan 1 vCPU, 1 GB RAM, 25 GB NVMe, dan transfer 2 TB per bulan. Akses root penuh, 1 alamat IPv4 dan IPv6, serta pilihan 33 region data center. Vultr menagih per jam dengan cap bulanan setara USD 6, sehingga harga yang ditampilkan adalah ekuivalen bulanan. Tanpa biaya setup dan instance bisa dihentikan kapan saja.',
                ],
                'en' => [
                    'excerpt' => 'VPS with 1 vCPU, 1 GB RAM, 25 GB NVMe, and 2 TB monthly transfer. High Performance AMD, billed monthly.',
                    'description' => 'Vultr Cloud Compute High Performance AMD plan with 1 vCPU, 1 GB RAM, 25 GB NVMe, and 2 TB monthly transfer. Includes full root access, 1 IPv4 and IPv6 address, and a choice of 33 data center regions. Vultr bills hourly with a monthly cap equal to USD 6, so the price shown is the monthly equivalent. No setup fee, and the instance can be stopped at any time.',
                ],
            ],
        ],
        [
            'platform' => 'vultr',
            'category' => 'vps',
            'slug' => 'vultr-cloud-compute-2gb',
            'title' => 'Vultr Cloud Compute 2 GB',
            'url' => 'https://www.vultr.com/?ref=7309768',
            'price' => 192000,
            'featured' => true,
            'specs' => [
                'cpu_cores' => 1,
                'ram_mb' => 2048,
                'storage_gb' => 50,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 3,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'location' => '33 region data center',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS 1 vCPU, 2 GB RAM, 50 GB NVMe, transfer 3 TB/bulan. Pilihan seimbang untuk website dan aplikasi.',
                    'description' => 'Paket Vultr Cloud Compute High Performance AMD dengan 1 vCPU, 2 GB RAM, 50 GB NVMe, dan transfer 3 TB per bulan. Cocok untuk website, aplikasi kecil, dan database ringan. Akses root penuh, 1 alamat IPv4 dan IPv6, serta pilihan 33 region data center. Tagihan per jam dengan cap bulanan setara USD 12, sehingga harga yang tampil adalah ekuivalen bulanan.',
                ],
                'en' => [
                    'excerpt' => 'VPS with 1 vCPU, 2 GB RAM, 50 GB NVMe, and 3 TB monthly transfer. A balanced choice for websites and apps.',
                    'description' => 'Vultr Cloud Compute High Performance AMD plan with 1 vCPU, 2 GB RAM, 50 GB NVMe, and 3 TB monthly transfer. It fits websites, small applications, and light databases. Includes full root access, 1 IPv4 and IPv6 address, and a choice of 33 data center regions. Billed hourly with a monthly cap equal to USD 12, so the price shown is the monthly equivalent.',
                ],
            ],
        ],
        [
            'platform' => 'vultr',
            'category' => 'vps',
            'slug' => 'vultr-cloud-compute-4gb',
            'title' => 'Vultr Cloud Compute 4 GB',
            'url' => 'https://www.vultr.com/?ref=7309768',
            'price' => 384000,
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 4096,
                'storage_gb' => 100,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 5,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'location' => '33 region data center',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS 2 vCPU, 4 GB RAM, 100 GB NVMe, transfer 5 TB/bulan. Untuk aplikasi dan database yang lebih berat.',
                    'description' => 'Paket Vultr Cloud Compute High Performance AMD dengan 2 vCPU, 4 GB RAM, 100 GB NVMe, dan transfer 5 TB per bulan. Cocok untuk aplikasi produksi, panel kontrol, dan database yang lebih besar. Akses root penuh, 1 alamat IPv4 dan IPv6, serta pilihan 33 region data center. Tagihan per jam dengan cap bulanan setara USD 24, sehingga harga yang tampil adalah ekuivalen bulanan.',
                ],
                'en' => [
                    'excerpt' => 'VPS with 2 vCPU, 4 GB RAM, 100 GB NVMe, and 5 TB monthly transfer. For heavier apps and databases.',
                    'description' => 'Vultr Cloud Compute High Performance AMD plan with 2 vCPU, 4 GB RAM, 100 GB NVMe, and 5 TB monthly transfer. It fits production applications, control panels, and larger databases. Includes full root access, 1 IPv4 and IPv6 address, and a choice of 33 data center regions. Billed hourly with a monthly cap equal to USD 24, so the price shown is the monthly equivalent.',
                ],
            ],
        ],

        // HostBrr — VPS, shared, dan reseller
        [
            'platform' => 'hostbrr',
            'category' => 'vps',
            'slug' => 'hostbrr-ryzen-5950x-2gb',
            'title' => 'HostBrr Ryzen 5950X 2 GB',
            'url' => 'https://my.hostbrr.com/order/forms/a/OTIx',
            'price' => 71840,
            'featured' => true,
            'specs' => [
                'cpu_cores' => 1,
                'ram_mb' => 2048,
                'storage_gb' => 40,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 5,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'location' => 'Falkenstein, Jerman',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS 1 vCore Ryzen 9 5950X, 2 GB RAM, 40 GB NVMe, bandwidth 5 TB, IPv4 & IPv6. USD 4,49/bulan.',
                    'description' => 'Paket VPS HostBrr 5950X dengan 1 vCore AMD Ryzen 9 5950X, 2 GB DDR4 ECC, 40 GB NVMe Gen4, dan bandwidth 5 TB pada port 1 Gbps. Termasuk 1 IPv4 dan IPv6/64, 1 slot backup, akses root penuh, dan lokasi Falkenstein, Jerman. Harga USD 4,49 per bulan; tersedia siklus tagihan 3 dan 12 bulan.',
                ],
                'en' => [
                    'excerpt' => 'VPS with 1 Ryzen 9 5950X core, 2 GB RAM, 40 GB NVMe, 5 TB bandwidth, IPv4 and IPv6. USD 4.49/month.',
                    'description' => 'HostBrr 5950X VPS plan with 1 AMD Ryzen 9 5950X core, 2 GB DDR4 ECC RAM, 40 GB Gen4 NVMe, and 5 TB of bandwidth on a 1 Gbps port. Includes 1 IPv4 and IPv6/64, 1 backup slot, full root access, and a Falkenstein, Germany location. Priced at USD 4.49 per month, with 3 and 12-month billing cycles available.',
                ],
            ],
        ],
        [
            'platform' => 'hostbrr',
            'category' => 'vps',
            'slug' => 'hostbrr-ryzen-5950x-4gb',
            'title' => 'HostBrr Ryzen 5950X 4 GB',
            'url' => 'https://my.hostbrr.com/order/forms/a/OTIx',
            'price' => 143840,
            'specs' => [
                'cpu_cores' => 2,
                'ram_mb' => 4096,
                'storage_gb' => 75,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 5,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'location' => 'Falkenstein, Jerman',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'VPS 2 vCore Ryzen 9 5950X, 4 GB RAM, 75 GB NVMe, bandwidth 5 TB, IPv4 & IPv6. USD 8,99/bulan.',
                    'description' => 'Paket VPS HostBrr 5950X dengan 2 vCore AMD Ryzen 9 5950X, 4 GB DDR4 ECC, 75 GB NVMe Gen4, dan bandwidth 5 TB pada port 1 Gbps. Termasuk 1 IPv4 dan IPv6/64, 1 slot backup, akses root penuh, dan lokasi Falkenstein, Jerman. Harga USD 8,99 per bulan; tersedia siklus tagihan 3 dan 12 bulan.',
                ],
                'en' => [
                    'excerpt' => 'VPS with 2 Ryzen 9 5950X cores, 4 GB RAM, 75 GB NVMe, 5 TB bandwidth, IPv4 and IPv6. USD 8.99/month.',
                    'description' => 'HostBrr 5950X VPS plan with 2 AMD Ryzen 9 5950X cores, 4 GB DDR4 ECC RAM, 75 GB Gen4 NVMe, and 5 TB of bandwidth on a 1 Gbps port. Includes 1 IPv4 and IPv6/64, 1 backup slot, full root access, and a Falkenstein, Germany location. Priced at USD 8.99 per month, with 3 and 12-month billing cycles available.',
                ],
            ],
        ],
        [
            'platform' => 'hostbrr',
            'category' => 'shared-hosting',
            'slug' => 'hostbrr-cpanel-25gb',
            'title' => 'HostBrr cPanel 25 GB',
            'url' => 'https://my.hostbrr.com/order/forms/a/OTIx',
            'price' => 56000,
            'specs' => [
                'storage_gb' => 25,
                'storage_type' => 'NVMe',
                'location' => 'Jerman, AS & Singapura',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'cPanel hosting 25 GB NVMe, bandwidth unmetered, domain tak terbatas, lokasi Jerman/AS/Singapura. USD 3,50/bulan.',
                    'description' => 'Paket cPanel Webhosting HostBrr dengan 25 GB penyimpanan NVMe Gen4, bandwidth unmetered, dan domain tak terbatas. Batas LVE per akun 3 CPU core dan 3 GB RAM. Termasuk cPanel, LiteSpeed, CloudLinux, AccelerateWP, Softaculous, SSL gratis, dan backup harian JetBackup. Pilihan lokasi server Jerman, AS, dan Singapura. Harga USD 3,50 per bulan; tersedia siklus tagihan 3, 6, dan 12 bulan.',
                ],
                'en' => [
                    'excerpt' => 'cPanel hosting with 25 GB NVMe, unmetered bandwidth, unlimited domains, and Germany/USA/Singapore locations. USD 3.50/month.',
                    'description' => 'HostBrr cPanel Webhosting plan with 25 GB of Gen4 NVMe storage, unmetered bandwidth, and unlimited domains. Per-account LVE limits are 3 CPU cores and 3 GB RAM. Includes cPanel, LiteSpeed, CloudLinux, AccelerateWP, Softaculous, free SSL, and daily JetBackup backups. Choose a server in Germany, the USA, or Singapore. Priced at USD 3.50 per month, with 3, 6, and 12-month billing cycles available.',
                ],
            ],
        ],
        [
            'platform' => 'hostbrr',
            'category' => 'reseller-hosting',
            'slug' => 'hostbrr-cpanel-reseller-50gb',
            'title' => 'HostBrr cPanel Reseller 50 GB',
            'url' => 'https://my.hostbrr.com/order/forms/a/OTIx',
            'price' => 144000,
            'specs' => [
                'storage_gb' => 50,
                'storage_type' => 'NVMe',
                'location' => 'Jerman, AS & Singapura',
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'cPanel reseller 50 GB NVMe untuk 10 akun, bandwidth unmetered, lokasi Jerman/AS/Singapura. USD 9/bulan.',
                    'description' => 'Paket cPanel Reseller Hosting HostBrr dengan 50 GB NVMe, bandwidth unmetered, dan 10 akun cPanel. Tambahan 5 akun dikenai USD 2,50 per bulan. Batas LVE per akun 4 CPU core dan 8 GB RAM. Termasuk WHM/cPanel, LiteSpeed, CloudLinux, AccelerateWP, Softaculous, SSL gratis, dan backup harian JetBackup. Lokasi server Jerman, AS, dan Singapura. Harga USD 9 per bulan; tersedia siklus tagihan 3, 6, dan 12 bulan.',
                ],
                'en' => [
                    'excerpt' => 'cPanel reseller with 50 GB NVMe for 10 accounts, unmetered bandwidth, and Germany/USA/Singapore locations. USD 9/month.',
                    'description' => 'HostBrr cPanel Reseller Hosting plan with 50 GB NVMe, unmetered bandwidth, and 10 cPanel accounts. Additional blocks of 5 accounts cost USD 2.50 per month. Per-account LVE limits are 4 CPU cores and 8 GB RAM. Includes WHM/cPanel, LiteSpeed, CloudLinux, AccelerateWP, Softaculous, free SSL, and daily JetBackup backups. Servers are available in Germany, the USA, and Singapore. Priced at USD 9 per month, with 3, 6, and 12-month billing cycles available.',
                ],
            ],
        ],

        // NameCrane — shared, cPanel reseller, DirectAdmin reseller
        [
            'platform' => 'namecrane',
            'category' => 'shared-hosting',
            'slug' => 'namecrane-beam-100',
            'title' => 'NameCrane Beam 100',
            'url' => 'https://namecrane.com/ref/nainpick/cpanel-shared-hosting',
            'price' => 112000,
            'featured' => true,
            'specs' => [
                'storage_gb' => 100,
                'storage_type' => 'NVMe',
                'ipv4_count' => 1,
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'cPanel shared hosting 100 GB NVMe, website tak terbatas, 4 core/6 GB RAM per akun, dedicated IP. USD 7/bulan.',
                    'description' => 'Paket cPanel Shared Hosting NameCrane Beam 100 yang ditandai paling populer memberi 100 GB penyimpanan NVMe, bandwidth unmetered, dan website tak terbatas. Batas resource per akun 4 CPU core dan 6 GB RAM, ditambah dedicated IPv4. Termasuk cPanel, LiteSpeed + LSCache, Redis, Imunify360, MXFilter, SSL gratis, backup harian dengan retensi 30 hari, dan migrasi gratis. Harga USD 7 per bulan; tersedia opsi tahunan USD 77 per tahun.',
                ],
                'en' => [
                    'excerpt' => 'cPanel shared hosting with 100 GB NVMe, unlimited websites, 4 cores/6 GB RAM per account, and a dedicated IP. USD 7/month.',
                    'description' => 'NameCrane Beam 100, marked as the most popular cPanel shared hosting plan, provides 100 GB of NVMe storage, unmetered bandwidth, and unlimited websites. Per-account limits are 4 CPU cores and 6 GB RAM, plus a dedicated IPv4 address. Includes cPanel, LiteSpeed + LSCache, Redis, Imunify360, MXFilter, free SSL, daily backups with 30-day retention, and free migration. Priced at USD 7 per month, with an annual option at USD 77 per year.',
                ],
            ],
        ],
        [
            'platform' => 'namecrane',
            'category' => 'reseller-hosting',
            'slug' => 'namecrane-cpanel-reseller-tower-500',
            'title' => 'NameCrane cPanel Reseller Tower 500',
            'url' => 'https://namecrane.com/ref/nainpick/cpanel-reseller-hosting',
            'price' => 640000,
            'featured' => true,
            'specs' => [
                'storage_gb' => 500,
                'storage_type' => 'NVMe',
                'ipv4_count' => 1,
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'cPanel reseller 500 GB NVMe untuk 40 akun, white-label, dedicated IP, Blesta gratis. USD 40/bulan.',
                    'description' => 'Paket cPanel Reseller NameCrane Tower 500 yang ditandai paling populer memberi 500 GB NVMe dan 40 akun cPanel. Setiap akun mendapat batas 2 CPU core dan 4 GB RAM, ditambah dedicated IP untuk akun reseller. Termasuk WHM, white-label branding, vanity nameserver, overselling, LiteSpeed, Redis, Imunify360, MXFilter, backup harian 30 hari, dan lisensi Blesta gratis. Harga USD 40 per bulan; tersedia opsi tahunan USD 440 per tahun.',
                ],
                'en' => [
                    'excerpt' => 'cPanel reseller with 500 GB NVMe for 40 accounts, white-label branding, dedicated IP, and a free Blesta license. USD 40/month.',
                    'description' => 'NameCrane cPanel Reseller Tower 500, marked as the most popular plan, provides 500 GB NVMe and 40 cPanel accounts. Each account gets 2 CPU cores and 4 GB RAM, plus a dedicated IP for the reseller account. Includes WHM, white-label branding, vanity nameservers, overselling, LiteSpeed, Redis, Imunify360, MXFilter, daily backups with 30-day retention, and a free Blesta license. Priced at USD 40 per month, with an annual option at USD 440 per year.',
                ],
            ],
        ],
        [
            'platform' => 'namecrane',
            'category' => 'reseller-hosting',
            'slug' => 'namecrane-directadmin-reseller-tower-500',
            'title' => 'NameCrane DirectAdmin Reseller Tower 500',
            'url' => 'https://namecrane.com/ref/nainpick/directadmin-reseller-hosting',
            'price' => 480000,
            'featured' => true,
            'specs' => [
                'storage_gb' => 500,
                'storage_type' => 'NVMe',
                'ipv4_count' => 1,
            ],
            'translations' => [
                'id' => [
                    'excerpt' => 'DirectAdmin reseller 500 GB NVMe untuk 200 sub-akun, white-label, dedicated IP, Blesta gratis. USD 30/bulan.',
                    'description' => 'Paket DirectAdmin Reseller NameCrane Tower 500 yang ditandai paling populer memberi 500 GB NVMe dan 200 sub-akun. Setiap akun mendapat batas 2 CPU core dan 4 GB RAM, ditambah dedicated IP untuk akun reseller. Termasuk akses reseller DirectAdmin, white-label branding, vanity nameserver, overselling, LiteSpeed, Redis, Imunify360, MXFilter, backup harian 30 hari, dan lisensi Blesta gratis. Harga USD 30 per bulan; tersedia opsi tahunan USD 330 per tahun.',
                ],
                'en' => [
                    'excerpt' => 'DirectAdmin reseller with 500 GB NVMe for 200 sub-accounts, white-label branding, dedicated IP, and a free Blesta license. USD 30/month.',
                    'description' => 'NameCrane DirectAdmin Reseller Tower 500, marked as the most popular plan, provides 500 GB NVMe and 200 sub-accounts. Each account gets 2 CPU cores and 4 GB RAM, plus a dedicated IP for the reseller account. Includes DirectAdmin reseller access, white-label branding, vanity nameservers, overselling, LiteSpeed, Redis, Imunify360, MXFilter, daily backups with 30-day retention, and a free Blesta license. Priced at USD 30 per month, with an annual option at USD 330 per year.',
                ],
            ],
        ],
    ];

    /**
     * @var array<string, string>
     */
    private array $platforms = [
        'domainesia' => 'DomaiNesia',
        'vultr' => 'Vultr',
        'hostbrr' => 'HostBrr',
        'namecrane' => 'NameCrane',
    ];

    /**
     * @var array<string, int>
     */
    private array $platformSortOrder = [
        'domainesia' => 11,
        'vultr' => 12,
        'hostbrr' => 13,
        'namecrane' => 14,
    ];

    /**
     * @var list<array{0: array{0: int, 1: int, 2: int}, 1: array{0: int, 1: int, 2: int}}>
     */
    private array $imagePalettes = [
        [[88, 28, 135], [168, 85, 247]],
        [[76, 29, 149], [139, 92, 246]],
        [[3, 105, 161], [56, 189, 248]],
        [[2, 62, 138], [14, 165, 233]],
        [[6, 78, 59], [16, 185, 129]],
        [[4, 47, 46], [20, 184, 166]],
        [[21, 94, 117], [6, 182, 212]],
        [[30, 58, 138], [59, 130, 246]],
        [[67, 20, 7], [217, 119, 6]],
        [[49, 46, 129], [99, 102, 241]],
        [[19, 78, 74], [45, 212, 191]],
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $platforms = $this->platforms($locales);
        $categories = $this->categories();

        foreach ($this->products as $index => $data) {
            $category = $categories[$data['category']] ?? null;
            $platform = $platforms[$data['platform']] ?? null;

            if ($category === null) {
                throw new RuntimeException("Kategori affiliate [{$data['category']}] belum ada. Jalankan AffiliateCategorySeeder lebih dulu.");
            }

            if ($platform === null) {
                throw new RuntimeException("Platform affiliate [{$data['platform']}] belum ada.");
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
                    'affiliate_url' => $data['url'],
                    'price' => $data['price'],
                    'original_price' => $data['original_price'] ?? null,
                    'billing_period' => 'monthly',
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
                    $changes['billing_period'] = 'monthly';
                }

                if ($product->original_price === null && isset($data['original_price'])) {
                    $changes['original_price'] = $data['original_price'];
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

    /**
     * @param  list<string>  $locales
     * @return array<string, AffiliatePlatform>
     */
    private function platforms(array $locales): array
    {
        $platforms = [];

        foreach ($this->platforms as $slug => $name) {
            $platform = AffiliatePlatform::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'is_active' => true,
                    'sort_order' => $this->platformSortOrder[$slug] ?? 99,
                ],
            );

            foreach ($locales as $locale) {
                if ($platform->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $platform->translateOrNew($locale)->fill(['name' => $name])->save();
            }

            $platforms[$slug] = $platform;
        }

        return $platforms;
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

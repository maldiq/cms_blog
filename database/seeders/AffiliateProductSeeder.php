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
     * Slug produk demo lama. Baris yang masih utuh (deskripsi contoh, tanpa
     * klik) dihapus agar katalog tidak lagi berisi "Contoh: ...". Baris yang
     * sudah diubah admin dibiarkan.
     *
     * @var list<string>
     */
    private const LEGACY_DEMO_SLUGS = [
        'contoh-headphone-bluetooth-shopee',
        'contoh-aksesori-gadget-shopee',
        'contoh-peralatan-rumah-shopee',
        'contoh-organizer-meja-tiktok',
        'contoh-perlengkapan-dapur-tiktok',
        'contoh-aksesori-kecantikan-tiktok',
        'contoh-buku-amazon',
        'contoh-aksesori-komputer-amazon',
        'contoh-vps-hostinger',
        'contoh-cloud-vps-digitalocean',
        'contoh-vps-vultr',
        'contoh-software-produktivitas',
        'contoh-perangkat-jaringan-shopee',
    ];

    /**
     * Katalog awal hasil riset tren publik (beauty, gadget, home, fashion,
     * otomotif). Harga adalah kisaran pasar, bukan harga real-time. Tautan
     * diarahkan ke pencarian marketplace sampai link affiliate diisi admin.
     *
     * @var list<array{title: string, slug: string, category: string, platform: string, price: int, original_price: int, description: string, featured?: bool, url?: string}>
     */
    private array $products = [
        // Kecantikan
        [
            'title' => 'Wardah Cushion Series',
            'slug' => 'wardah-cushion-series',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 129000,
            'original_price' => 159000,
            'description' => 'Cushion lokal dengan hasil akhir natural dan coverage ringan. Cocok untuk konten perbandingan shade dan tips tahan lama di cuaca panas.',
            'featured' => true,
        ],
        [
            'title' => 'Hanasui Blush & Lip Cream',
            'slug' => 'hanasui-blush-lip-cream',
            'category' => 'Kecantikan',
            'platform' => 'tiktok_shop',
            'price' => 35000,
            'original_price' => 45000,
            'description' => 'Blush dan lip cream dua fungsi dengan harga di bawah Rp50 ribu. Konten "satu produk dua fungsi" mudah dibuat dan sering menyebar di TikTok.',
        ],
        [
            'title' => 'Maybelline Superstay Matte Ink',
            'slug' => 'maybelline-superstay-matte-ink',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 99000,
            'original_price' => 129000,
            'description' => 'Lipstik cair matte yang dikenal tahan lama. Cocok untuk uji ketahanan setelah makan dan minum, format konten yang mudah ditonton ulang.',
            'featured' => true,
        ],
        [
            'title' => 'Skintific 5X Ceramide Moisturizer',
            'slug' => 'skintific-5x-ceramide-moisturizer',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 139000,
            'original_price' => 179000,
            'description' => 'Pelembap dengan ceramide untuk menjaga skin barrier. Paling relevan untuk audiens kulit sensitif dan pengguna aktif yang butuh produk pemulih.',
            'featured' => true,
        ],
        [
            'title' => 'Azarine Hydrasoothe Sunscreen Gel SPF50',
            'slug' => 'azarine-hydrasoothe-sunscreen-spf50',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 69000,
            'original_price' => 89000,
            'description' => 'Sunscreen gel SPF50 yang ringan dan tidak lengket. Angle konten: rutinitas pagi, reapply di luar ruangan, dan perbandingan tekstur.',
        ],
        [
            'title' => 'Avoskin Miraculous Refining Toner',
            'slug' => 'avoskin-miraculous-refining-toner',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 179000,
            'original_price' => 229000,
            'description' => 'Toner eksfoliasi untuk membantu tekstur kulit dan pori-pori. Cocok untuk konten penjelasan bahan aktif dan cara memakai tanpa iritasi.',
        ],
        [
            'title' => 'Somethinc Niacinamide + Moisture Sabi Beet Serum',
            'slug' => 'somethinc-niacinamide-sabi-beet-serum',
            'category' => 'Kecantikan',
            'platform' => 'tiktok_shop',
            'price' => 99000,
            'original_price' => 129000,
            'description' => 'Serum niacinamide untuk membantu menyamarkan bekas jerawat dan menjaga kelembapan. Sering dicari lewat pencarian, bukan hanya lewat konten viral.',
        ],
        [
            'title' => 'Scarlett Whitening Body Lotion',
            'slug' => 'scarlett-whitening-body-lotion',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 75000,
            'original_price' => 95000,
            'description' => 'Body lotion perawatan kulit tubuh dengan repeat order tinggi. Cocok untuk konten rutinitas malam dan perbandingan sebelum-sesudah pemakaian rutin.',
        ],
        [
            'title' => 'Make Over Powerstay Matte Powder Foundation',
            'slug' => 'make-over-powerstay-matte-powder-foundation',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 159000,
            'original_price' => 189000,
            'description' => 'Bedak foundation lokal dengan hasil matte. Berguna untuk konten coverage, ketahanan seharian, dan pemilihan shade untuk kulit Indonesia.',
        ],
        [
            'title' => 'Luxcrime Blur & Cover Powder',
            'slug' => 'luxcrime-blur-cover-powder',
            'category' => 'Kecantikan',
            'platform' => 'tiktok_shop',
            'price' => 89000,
            'original_price' => 109000,
            'description' => 'Bedak tabur dengan efek blur. Format before-after dan uji kamera depan sangat cocok untuk audiens TikTok yang mencari hasil foto natural.',
        ],
        [
            'title' => 'Npure Niacinamide 10% Serum',
            'slug' => 'npure-niacinamide-10-serum',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 79000,
            'original_price' => 99000,
            'description' => 'Serum niacinamide dengan harga menengah. Pilihan aman untuk audiens yang baru mulai merawat bekas jerawat tanpa budget besar.',
        ],
        [
            'title' => 'Implora Acne Serum',
            'slug' => 'implora-acne-serum',
            'category' => 'Kecantikan',
            'platform' => 'shopee',
            'price' => 45000,
            'original_price' => 59000,
            'description' => 'Serum jerawat harga terjangkau untuk audiens pelajar dan mahasiswa. Konten yang relevan: rutinitas sederhana dan tips menghindari iritasi.',
        ],

        // Elektronik & gadget
        [
            'title' => 'Jisulife Life 9 Portable Fan',
            'slug' => 'jisulife-life-9-portable-fan',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 179000,
            'original_price' => 229000,
            'description' => 'Kipas portabel dengan baterai besar dan lipatan fleksibel. Produk musiman yang mudah dipromosikan saat cuaca panas dan musim mudik.',
            'featured' => true,
        ],
        [
            'title' => 'UGREEN 65W GaN Charger',
            'slug' => 'ugreen-65w-gan-charger',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 239000,
            'original_price' => 299000,
            'description' => 'Charger GaN ringkas untuk laptop dan ponsel sekaligus. Angle konten: "satu charger untuk semua" dan perbandingan ukuran dengan charger bawaan.',
        ],
        [
            'title' => 'Anker PowerCore 10000mAh',
            'slug' => 'anker-powercore-10000mah',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 279000,
            'original_price' => 349000,
            'description' => 'Power bank ringkas dari merek aksesori yang sudah dikenal. Pembelian cenderung berdasarkan pencarian merek, bukan impulse.',
        ],
        [
            'title' => 'Baseus USB-C 100W Cable',
            'slug' => 'baseus-usb-c-100w-cable',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 49000,
            'original_price' => 79000,
            'description' => 'Kabel USB-C 100W untuk pengisian cepat. Produk pelengkap yang bagus untuk bundling dengan charger dan power bank.',
        ],
        [
            'title' => 'Soundcore R50i TWS',
            'slug' => 'soundcore-r50i-tws',
            'category' => 'Elektronik',
            'platform' => 'tiktok_shop',
            'price' => 229000,
            'original_price' => 299000,
            'description' => 'Earbuds TWS dengan bass kuat dan baterai panjang. Cocok untuk konten uji suara, latensi gaming, dan kenyamanan saat olahraga.',
        ],
        [
            'title' => 'Xiaomi Redmi Buds 6 Play',
            'slug' => 'xiaomi-redmi-buds-6-play',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 179000,
            'original_price' => 229000,
            'description' => 'Earbuds entry-level dari ekosistem Xiaomi. Kekuatan kontennya ada pada harga, koneksi cepat, dan aplikasi pengaturan.',
        ],
        [
            'title' => 'Logitech M170 Wireless Mouse',
            'slug' => 'logitech-m170-wireless-mouse',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 119000,
            'original_price' => 149000,
            'description' => 'Mouse wireless sederhana untuk kerja dan belajar. Evergreen: orang terus mencari mouse murah yang baterainya awet.',
        ],
        [
            'title' => 'JBL Go 3 Portable Speaker',
            'slug' => 'jbl-go-3-portable-speaker',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 499000,
            'original_price' => 649000,
            'description' => 'Speaker portabel tahan air dengan merek yang sudah kuat. Cocok untuk konten perbandingan suara dan penggunaan outdoor.',
        ],
        [
            'title' => 'Xiaomi Smart Band 8',
            'slug' => 'xiaomi-smart-band-8',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 339000,
            'original_price' => 399000,
            'description' => 'Smartband dengan layar memanjang dan banyak mode olahraga. Angle konten: pemantauan tidur, langkah harian, dan baterai tahan lama.',
        ],
        [
            'title' => 'HUAWEI Band 9',
            'slug' => 'huawei-band-9',
            'category' => 'Elektronik',
            'platform' => 'tiktok_shop',
            'price' => 379000,
            'original_price' => 499000,
            'description' => 'Smartband ringan dengan fokus kesehatan dan kenyamanan tidur. Bagus untuk audiens yang membandingkan smartband sebelum membeli smartwatch.',
        ],
        [
            'title' => 'Deerma Vacuum Cleaner Portable',
            'slug' => 'deerma-vacuum-cleaner-portable',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 399000,
            'original_price' => 549000,
            'description' => 'Vacuum cleaner genggam untuk sisa makanan dan debu mobil. Konten "bersih-bersih cepat" punya daya tarik visual tinggi.',
        ],
        [
            'title' => 'Xiaomi Power Bank 10000mAh 22.5W',
            'slug' => 'xiaomi-power-bank-10000mah-22-5w',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'price' => 169000,
            'original_price' => 219000,
            'description' => 'Power bank 10.000 mAh dengan pengisian cepat. Pilihan aman untuk perjalanan dan aktivitas luar ruangan.',
        ],

        // Rumah tangga
        [
            'title' => 'Spin Mop 360 Set',
            'slug' => 'spin-mop-360-set',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'price' => 129000,
            'original_price' => 169000,
            'description' => 'Alat pel dengan pemeras putar dan refill microfiber. Konten sebelum-sesudah mengepel cepat menarik untuk audiens ibu rumah tangga.',
        ],
        [
            'title' => 'Tempat Sampah Sensor 12L',
            'slug' => 'tempat-sampah-sensor-12l',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'price' => 229000,
            'original_price' => 299000,
            'description' => 'Tempat sampah otomatis dengan sensor gerak. Masuk kategori "home gadget" yang mudah didemokan dalam video pendek.',
        ],
        [
            'title' => 'Lampu Tidur Sensor Gerak',
            'slug' => 'lampu-tidur-sensor-gerak',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'price' => 49000,
            'original_price' => 69000,
            'description' => 'Lampu kecil dengan sensor gerak untuk kamar atau lorong. Harga murah membuat keputusan pembelian cepat.',
        ],
        [
            'title' => 'Setrika Uap Handheld',
            'slug' => 'setrika-uap-handheld',
            'category' => 'Rumah Tangga',
            'platform' => 'tiktok_shop',
            'price' => 189000,
            'original_price' => 249000,
            'description' => 'Setrika uap genggam untuk merapikan baju tanpa papan setrika. Visual uapnya cocok untuk video demo singkat.',
        ],
        [
            'title' => 'Jemuran Lipat Stainless',
            'slug' => 'jemuran-lipat-stainless',
            'category' => 'Rumah Tangga',
            'platform' => 'shopee',
            'price' => 159000,
            'original_price' => 199000,
            'description' => 'Jemuran lipat dari stainless yang mudah disimpan. Relevan untuk rumah kecil dan apartemen.',
        ],

        // Dapur
        [
            'title' => 'OXONE Chopper OX-151',
            'slug' => 'oxone-chopper-ox-151',
            'category' => 'Dapur',
            'platform' => 'shopee',
            'price' => 165000,
            'original_price' => 210000,
            'description' => 'Chopper manual untuk bawang, cabai, dan bumbu dapur. Merek lokal yang sudah dikenal dan sering dicari lewat pencarian.',
        ],
        [
            'title' => 'Lock & Lock Food Container Set',
            'slug' => 'lock-lock-food-container-set',
            'category' => 'Dapur',
            'platform' => 'shopee',
            'price' => 149000,
            'original_price' => 189000,
            'description' => 'Set wadah makanan kedap udara untuk meal prep dan stok bahan. Konten organizer dapur selalu punya peminat.',
        ],
        [
            'title' => 'Kotak Makan Stainless 3 Sekat',
            'slug' => 'kotak-makan-stainless-3-sekat',
            'category' => 'Dapur',
            'platform' => 'tiktok_shop',
            'price' => 69000,
            'original_price' => 89000,
            'description' => 'Lunch box stainless dengan sekat untuk bekal kerja atau sekolah. Cocok untuk konten meal prep mingguan.',
        ],
        [
            'title' => 'Teflon Granite 28cm',
            'slug' => 'teflon-granite-28cm',
            'category' => 'Dapur',
            'platform' => 'shopee',
            'price' => 129000,
            'original_price' => 169000,
            'description' => 'Wajan anti lengket dengan lapisan granite. Konten demo masak tanpa minyak banyak membantu konversi.',
        ],
        [
            'title' => 'Dispenser Sabun Cuci Piring Otomatis',
            'slug' => 'dispenser-sabun-cuci-piring-otomatis',
            'category' => 'Dapur',
            'platform' => 'shopee',
            'price' => 59000,
            'original_price' => 79000,
            'description' => 'Dispenser otomatis untuk sabun cuci piring atau handsanitizer. Barang kecil dengan harga murah, cocok untuk konten "kerapian dapur".',
        ],

        // Fashion
        [
            'title' => 'Hijab Bergo Instan Jersey',
            'slug' => 'hijab-bergo-instan-jersey',
            'category' => 'Fashion',
            'platform' => 'shopee',
            'price' => 45000,
            'original_price' => 65000,
            'description' => 'Bergo instan bahan jersey yang praktis dipakai harian. Volume pencarian besar, kompetisi juga tinggi, jadi konten styling perlu dibedakan.',
            'featured' => true,
        ],
        [
            'title' => 'Gamis Syari Set',
            'slug' => 'gamis-syari-set',
            'category' => 'Fashion',
            'platform' => 'tiktok_shop',
            'price' => 179000,
            'original_price' => 239000,
            'description' => 'Set gamis syari untuk acara dan hari raya. Nilai transaksi lebih tinggi, tapi butuh konten styling dan detail bahan.',
        ],
        [
            'title' => 'Kaos Oversize Cotton Combed 30s',
            'slug' => 'kaos-oversize-cotton-combed-30s',
            'category' => 'Fashion',
            'platform' => 'shopee',
            'price' => 65000,
            'original_price' => 85000,
            'description' => 'Kaos oversize bahan cotton combed yang sering dicari untuk outfit harian. Konten perbandingan ketebalan bahan membantu pembeli memilih.',
        ],
        [
            'title' => 'Kemeja Linen Casual',
            'slug' => 'kemeja-linen-casual',
            'category' => 'Fashion',
            'platform' => 'shopee',
            'price' => 119000,
            'original_price' => 159000,
            'description' => 'Kemeja linen untuk gaya kasual dan kerja. Cocok untuk konten mix and match dengan celana yang sudah dimiliki pembeli.',
        ],
        [
            'title' => 'Celana Cargo Jogger',
            'slug' => 'celana-cargo-jogger',
            'category' => 'Fashion',
            'platform' => 'tiktok_shop',
            'price' => 109000,
            'original_price' => 149000,
            'description' => 'Celana cargo jogger dengan banyak kantong. Tren streetwear membuat produk ini sering muncul di video outfit.',
        ],
        [
            'title' => 'Sandal Slide EVA',
            'slug' => 'sandal-slide-eva',
            'category' => 'Fashion',
            'platform' => 'shopee',
            'price' => 49000,
            'original_price' => 79000,
            'description' => 'Sandal ringan dari bahan EVA untuk harian dan kamar mandi. Harga murah dan visual simpel, cocok untuk bundling.',
        ],

        // Otomotif
        [
            'title' => 'Sarung Tangan Motor Anti Slip',
            'slug' => 'sarung-tangan-motor-anti-slip',
            'category' => 'Otomotif',
            'platform' => 'shopee',
            'price' => 35000,
            'original_price' => 49000,
            'description' => 'Sarung tangan riding dengan telapak anti slip. Kategori otomotif tumbuh cepat dan kompetisi kontennya masih lebih longgar.',
        ],
        [
            'title' => 'Holder HP Motor Anti Getar',
            'slug' => 'holder-hp-motor-anti-getar',
            'category' => 'Otomotif',
            'platform' => 'shopee',
            'price' => 69000,
            'original_price' => 99000,
            'description' => 'Penyangga ponsel untuk motor dengan peredam getaran. Sangat relevan untuk pengemudi ojek online dan pengguna navigasi.',
        ],
        [
            'title' => 'Jas Hujan Ponco Motor',
            'slug' => 'jas-hujan-ponco-motor',
            'category' => 'Otomotif',
            'platform' => 'tiktok_shop',
            'price' => 79000,
            'original_price' => 109000,
            'description' => 'Jas hujan model ponco yang menutupi tangan dan tas. Permintaan naik saat musim hujan, cocok untuk konten musiman.',
        ],
        [
            'title' => 'Cover Motor Waterproof',
            'slug' => 'cover-motor-waterproof',
            'category' => 'Otomotif',
            'platform' => 'shopee',
            'price' => 69000,
            'original_price' => 95000,
            'description' => 'Sarung motor tahan air untuk parkir luar ruangan. Produk kebutuhan yang jarang dibahas konten, tapi pencariannya stabil.',
        ],

        // Hosting & VPS
        [
            'title' => 'Hostinger VPS',
            'slug' => 'hostinger-vps',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'price' => 104000,
            'original_price' => 312000,
            'legacy_price' => 65000,
            'description' => 'VPS KVM untuk website dan aplikasi kecil. Paket entry-level sudah mendapat 4 GB RAM dan penyimpanan NVMe, cukup untuk beberapa aplikasi ringan sekaligus. Harga promo mengikuti pembayaran di muka 24 bulan.',
            'url' => 'https://www.hostinger.com/vps-hosting',
            'specs' => [
                'billing_period' => 'monthly',
                'cpu_cores' => 1,
                'ram_mb' => 4096,
                'storage_gb' => 50,
                'storage_type' => 'NVMe',
                'bandwidth_tb' => 4,
                'ipv4_count' => 1,
                'ipv6_support' => null,
                'port_speed' => '1Gbps',
                'location' => '10 lokasi (termasuk Jakarta)',
            ],
            'last_checked_at' => '2026-11-20',
        ],
        [
            'title' => 'DigitalOcean Droplets',
            'slug' => 'digitalocean-droplets',
            'category' => 'Hosting & VPS',
            'platform' => 'vps',
            'price' => 96000,
            'original_price' => null,
            'legacy_price' => 88000,
            'description' => 'Cloud VPS dengan tagihan per jam untuk developer. Cocok untuk tutorial deployment, eksperimen singkat, dan panduan biaya server.',
            'url' => 'https://www.digitalocean.com/pricing/droplets',
            'specs' => [
                'billing_period' => 'monthly',
                'cpu_cores' => 1,
                'ram_mb' => 1024,
                'storage_gb' => 25,
                'storage_type' => 'SSD',
                'bandwidth_tb' => 1,
                'ipv4_count' => 1,
                'ipv6_support' => true,
                'port_speed' => null,
                'location' => 'Singapore',
            ],
            'last_checked_at' => '2026-11-20',
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

        $this->removeLegacyDemoProducts();

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
                throw new \RuntimeException("Kategori atau platform tidak tersedia untuk produk {$product['slug']}.");
            }

            $existing = AffiliateProduct::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $product['slug']))
                ->first();

            $existing ??= AffiliateProduct::query()->create([
                'affiliate_category_id' => $categoryId,
                'affiliate_platform_id' => $platformId,
                'affiliate_url' => $product['url'] ?? $this->marketplaceSearchUrl($product),
                'price' => $product['price'],
                'original_price' => $product['original_price'],
                'is_featured' => (bool) ($product['featured'] ?? false),
                'is_active' => true,
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

            // Lengkapi spesifikasi hanya bila baris belum punya data spek sama
            // sekali, agar angka yang sudah dikoreksi admin tidak tertimpa.
            $hasSpecs = collect(['cpu_cores', 'ram_mb', 'storage_gb', 'bandwidth_tb'])
                ->contains(fn (string $field): bool => $existing->{$field} !== null);

            if (! $hasSpecs && isset($product['specs'])) {
                $changes = array_merge($changes, $product['specs'], [
                    'last_checked_at' => $product['last_checked_at'] ?? now(),
                ]);
            }

            if (config('affiliate.seed_demo_images', true) && $existing->image_id === null) {
                [$from, $to] = $this->imagePalettes[$index % count($this->imagePalettes)];
                $imagePath = $this->generateDemoImage(
                    "affiliate-product-{$product['slug']}.jpg",
                    800,
                    800,
                    $from,
                    $to,
                );
                $changes['image_id'] = app(MediaUploadService::class)->uploadFromPath($imagePath)->getKey();
            }

            // Hanya isi produk yang belum disentuh admin sama sekali agar data
            // hasil kurasi tidak tertimpa saat re-seed.
            if ($existing->price === null) {
                $changes['price'] = $product['price'];
                $changes['original_price'] = $product['original_price'];

                if ((bool) ($product['featured'] ?? false)) {
                    $changes['is_featured'] = true;
                }
            }

            if (isset($product['legacy_price']) && (float) $existing->price === (float) $product['legacy_price']) {
                $changes['price'] = $product['price'];
                $changes['original_price'] = $product['original_price'];
            }

            if ($changes !== []) {
                $existing->update($changes);
            }
        }
    }

    /**
     * Tautan sementara ke halaman pencarian marketplace. Admin menggantinya
     * dengan link affiliate saat sudah tersedia.
     *
     * @param  array{title: string, platform: string}  $product
     */
    private function marketplaceSearchUrl(array $product): string
    {
        $keyword = rawurlencode($product['title']);

        return match ($product['platform']) {
            'shopee' => "https://shopee.co.id/search?keyword={$keyword}",
            'tiktok_shop' => "https://www.tiktok.com/search?q={$keyword}",
            default => 'https://shopee.co.id/search?keyword='.$keyword,
        };
    }

    /**
     * Hapus sisa produk demo lama yang masih utuh. Baris yang sudah punya
     * klik tidak dihapus agar riwayat laporan tetap utuh — cukup
     * dinonaktifkan. Baris yang sudah diubah admin dibiarkan apa adanya.
     */
    private function removeLegacyDemoProducts(): void
    {
        $legacy = AffiliateProduct::query()
            ->whereHas('translations', fn ($query) => $query->whereIn('slug', self::LEGACY_DEMO_SLUGS))
            ->with('translations')
            ->get();

        foreach ($legacy as $product) {
            $descriptions = $product->translations
                ->map(fn ($translation): string => (string) $translation->description)
                ->all();

            $looksUntouched = collect($descriptions)
                ->contains(fn (string $description): bool => str_starts_with($description, 'Data contoh'));

            if (! $looksUntouched) {
                continue;
            }

            if ($product->clicks()->exists()) {
                if ($product->is_active) {
                    $product->update(['is_active' => false]);
                }

                continue;
            }

            $product->delete();
        }
    }
}

<?php

namespace Database\Seeders\Support;

/**
 * Konten editorial kategori hosting. Dipakai oleh AffiliateCategorySeeder
 * (instalasi baru) dan migrasi refresh editorial (instalasi yang sudah ada).
 */
class HostingEditorial
{
    /**
     * @return array<string, array<string, array{intro: string, meta_title: string, meta_description: string, faq: list<array{question: string, answer: string}>}>>
     */
    public static function all(): array
    {
        return [
            'VPS' => [
                'id' => [
                    'intro' => "Kategori VPS di NainPicks merangkum paket server virtual dari beberapa penyedia: promo tahunan RackNerd, cloud compute Vultr dengan tagihan bulanan, paket bulanan Hostinger, dan Droplet DigitalOcean, serta promo anniversary HostHatch dengan tagihan tahunan. Fokusnya bukan hanya harga termurah, tetapi resource yang benar-benar didapat: vCPU, RAM, penyimpanan, bandwidth, dan periode tagihan.\n\nPerhatikan periode tagihan. Vultr, Hostinger, dan DigitalOcean menagih bulanan (Vultr dihitung per jam dengan cap bulanan), sementara promo RackNerd dan HostHatch ditagih per tahun di muka, sehingga angka yang tampil adalah harga setahun. Untuk membandingkan biaya bulanan, bagi harga tahunan dengan 12.\n\nUntuk blog atau company profile, 1 vCPU dengan RAM 1–2 GB biasanya cukup. Naikkan RAM dan penyimpanan jika menjalankan panel kontrol, database, Docker, atau beberapa aplikasi sekaligus. Selalu cek ulang halaman penyedia sebelum membeli karena promo, stok lokasi, dan kurs dapat berubah.",
                    'meta_title' => 'VPS Murah: Harga, Spesifikasi, dan Perbandingan Paket',
                    'meta_description' => 'Bandingkan paket VPS dari RackNerd, Vultr, Hostinger, DigitalOcean, dan HostHatch: vCPU, RAM, penyimpanan NVMe/SSD, bandwidth, lokasi, dan periode tagihan. Cek sebelum membeli.',
                    'faq' => [
                        [
                            'question' => 'Berapa RAM yang dibutuhkan untuk VPS website?',
                            'answer' => 'Untuk blog atau company profile, 1–2 GB RAM biasanya cukup. Gunakan 4 GB atau lebih jika menjalankan panel kontrol, database, Docker, atau beberapa aplikasi bersamaan.',
                        ],
                        [
                            'question' => 'Apakah harga VPS di halaman ini sudah termasuk pajak?',
                            'answer' => 'Belum tentu. Harga mengikuti halaman resmi penyedia dan bisa berbeda karena promo, kurs, pajak, serta biaya tambahan seperti backup atau IPv4 tambahan.',
                        ],
                        [
                            'question' => 'Kenapa ada harga tahunan dan harga bulanan?',
                            'answer' => 'Vultr, Hostinger, dan DigitalOcean menagih bulanan, sementara promo Black Friday RackNerd dan anniversary HostHatch menagih setahun di muka. Bagi harga tahunan dengan 12 untuk melihat ekuivalen bulanannya.',
                        ],
                        [
                            'question' => 'Kenapa harga promo VPS terlihat lebih murah?',
                            'answer' => 'Harga promo umumnya berlaku untuk pembayaran di muka, misalnya 12–24 bulan. Harga perpanjangan bisa lebih tinggi, jadi bandingkan total biaya, bukan hanya angka di bulan pertama.',
                        ],
                    ],
                ],
                'en' => [
                    'intro' => "The VPS category on NainPicks gathers virtual server plans from several providers: RackNerd's annual promos, Vultr cloud compute billed monthly, Hostinger's monthly plans, DigitalOcean Droplets, and HostHatch's annual anniversary promo. The focus is not just the lowest sticker price, but the resources you actually get: vCPU, RAM, storage, bandwidth, and billing period.\n\nPay attention to the billing period. Vultr, Hostinger, and DigitalOcean bill monthly (Vultr is metered hourly with a monthly cap), while RackNerd and HostHatch promos require an annual upfront payment, so the figure shown is the yearly price. Divide the annual price by 12 to compare monthly costs.\n\nFor a blog or company profile, 1 vCPU with 1–2 GB of RAM is usually enough. Add more RAM and storage when running a control panel, database, Docker, or several apps at once. Always recheck the provider page before buying, as promos, location availability, and exchange rates change.",
                    'meta_title' => 'Affordable VPS: Pricing, Specs, and Plan Comparison',
                    'meta_description' => 'Compare VPS plans from RackNerd, Vultr, Hostinger, DigitalOcean, and HostHatch: vCPU, RAM, NVMe or SSD storage, bandwidth, location, and billing period. Check the specs before you buy.',
                    'faq' => [
                        [
                            'question' => 'How much RAM does a website VPS need?',
                            'answer' => 'A blog or company profile usually runs fine on 1–2 GB of RAM. Use 4 GB or more when running a control panel, database, Docker, or several apps together.',
                        ],
                        [
                            'question' => 'Do the VPS prices on this page include tax?',
                            'answer' => 'Not necessarily. Prices follow the provider page and may differ due to promos, exchange rates, taxes, and add-ons such as backups or extra IPv4 addresses.',
                        ],
                        [
                            'question' => 'Why are some prices yearly and others monthly?',
                            'answer' => 'Vultr, Hostinger, and DigitalOcean bill monthly, while RackNerd Black Friday and HostHatch anniversary promos require an annual upfront payment. Divide the annual price by 12 to see the monthly equivalent.',
                        ],
                        [
                            'question' => 'Why do VPS promo prices look so cheap?',
                            'answer' => 'Promo pricing usually requires an upfront payment, for example 12–24 months. Renewal pricing can be higher, so compare the total cost rather than the first-month figure.',
                        ],
                    ],
                ],
            ],
            'Shared Hosting' => [
                'id' => [
                    'intro' => "Shared hosting cocok untuk website, blog, dan toko online skala kecil karena server, panel, dan pembaruan keamanan diurus penyedia. Kategori ini menggabungkan paket Nimbus dari DomaiNesia dengan harga rupiah bulanan, cPanel RackNerd promo tahunan, cPanel HostBrr dengan lokasi Jerman/AS/Singapura, dan cPanel NameCrane dengan resource per akun yang besar.\n\nYang membedakan paket biasanya penyimpanan NVMe, jumlah website, batas CPU/RAM per akun, bandwidth, dan lokasi server. Nimbus One cocok untuk satu website kecil, Nimbus Go dan Plus untuk beberapa website, sedangkan paket HostBrr dan NameCrane memberi batas resource per akun yang lebih eksplisit.\n\nPerhatikan periode tagihan dan harga perpanjangan. Sebagian paket ditagih bulanan, sementara promo RackNerd ditagih tahunan di muka. Selalu cek kuota resource dan kebijakan penyedia sebelum membeli.",
                    'meta_title' => 'Shared Hosting Murah: Harga dan Perbandingan Paket',
                    'meta_description' => 'Bandingkan shared hosting DomaiNesia, RackNerd, HostBrr, dan NameCrane: penyimpanan NVMe, jumlah website, batas CPU/RAM, bandwidth, lokasi server, dan periode tagihan.',
                    'faq' => [
                        [
                            'question' => 'Apa itu shared hosting?',
                            'answer' => 'Shared hosting menempatkan beberapa website pada satu server dan berbagi resource CPU, RAM, serta penyimpanan. Penyedia mengurus server dan pembaruan keamanan, sehingga lebih mudah untuk pemula.',
                        ],
                        [
                            'question' => 'Bagaimana memilih paket shared hosting?',
                            'answer' => 'Lihat jumlah website, penyimpanan NVMe, batas CPU/RAM per akun, dan lokasi server. Paket DomaiNesia cocok untuk pengunjung Indonesia, sedangkan HostBrr dan NameCrane punya pilihan lokasi dan batas resource yang berbeda.',
                        ],
                        [
                            'question' => 'Apakah shared hosting cukup untuk toko online?',
                            'answer' => 'Untuk toko online kecil dengan trafik moderat, shared hosting biasanya cukup. Jika trafik dan plugin bertambah, pertimbangkan VPS agar resource tidak berebut dengan pengguna lain.',
                        ],
                        [
                            'question' => 'Apakah harga sudah termasuk domain?',
                            'answer' => 'Tidak selalu. DomaiNesia mencantumkan domain gratis untuk pembelian hosting minimal 1 tahun pada paket tertentu. Paket lain mengikuti halaman penyedia saat checkout.',
                        ],
                    ],
                ],
                'en' => [
                    'intro' => "Shared hosting suits small websites, blogs, and online stores because the provider manages the server, panel, and security updates. This category combines DomaiNesia's Nimbus plans priced in rupiah per month, RackNerd's annual cPanel promo, HostBrr cPanel hosting in Germany/USA/Singapore, and NameCrane cPanel hosting with generous per-account resources.\n\nPlans differ mainly by NVMe storage, website count, per-account CPU/RAM limits, bandwidth, and server location. Nimbus One fits a single small site, Nimbus Go and Plus support multiple sites, while HostBrr and NameCrane state per-account resource limits more explicitly.\n\nPay attention to the billing period and renewal price. Some plans are billed monthly, while the RackNerd promo is billed annually upfront. Always recheck resource quotas and provider policies before buying.",
                    'meta_title' => 'Affordable Shared Hosting: Pricing and Plan Comparison',
                    'meta_description' => 'Compare shared hosting from DomaiNesia, RackNerd, HostBrr, and NameCrane: NVMe storage, website count, CPU/RAM limits, bandwidth, server location, and billing period.',
                    'faq' => [
                        [
                            'question' => 'What is shared hosting?',
                            'answer' => 'Shared hosting places several websites on one server and shares CPU, RAM, and storage resources. The provider manages the server and security updates, making it easier for beginners.',
                        ],
                        [
                            'question' => 'How do I choose a shared hosting plan?',
                            'answer' => 'Look at the website count, NVMe storage, per-account CPU/RAM limits, and server location. DomaiNesia plans fit Indonesian visitors, while HostBrr and NameCrane offer different locations and resource limits.',
                        ],
                        [
                            'question' => 'Is shared hosting enough for an online store?',
                            'answer' => 'For a small store with moderate traffic, shared hosting is usually enough. As traffic and plugins grow, consider a VPS so resources are not shared with other users.',
                        ],
                        [
                            'question' => 'Does the price include a domain?',
                            'answer' => 'Not necessarily. DomaiNesia lists a free domain with hosting purchases of at least one year on selected plans. Other plans follow the provider page at checkout.',
                        ],
                    ],
                ],
            ],
            'Reseller Hosting' => [
                'id' => [
                    'intro' => "Reseller hosting dipakai untuk membangun layanan hosting sendiri: Anda membeli paket besar, lalu membaginya menjadi beberapa akun untuk klien. Kategori ini mencakup RackNerd dengan panel AdminBolt dan lisensi Clientexec, NameCrane cPanel dan DirectAdmin dengan akses reseller WHM/DirectAdmin serta Blesta gratis, dan HostBrr cPanel reseller dengan paket akun tambahan.\n\nPaket dibedakan oleh total penyimpanan NVMe, jumlah akun yang bisa dibuat, batas CPU/RAM per akun, dan fitur white-label. RackNerd menyediakan 15–50 akun, HostBrr 10 akun pada paket 50 GB, sedangkan NameCrane menyediakan 20–160 akun cPanel atau 25–500 sub-akun DirectAdmin tergantung paket.\n\nPerhatikan periode tagihan, kebijakan overselling, dan lisensi software billing. Harga promo RackNerd ditagih tahunan, sementara paket HostBrr dan NameCrane ditagih bulanan dengan opsi tahunan.",
                    'meta_title' => 'Reseller Hosting Murah: Harga dan Perbandingan Paket',
                    'meta_description' => 'Bandingkan reseller hosting RackNerd, NameCrane, dan HostBrr: penyimpanan NVMe, jumlah akun, panel white-label, resource per akun, dan lisensi software billing.',
                    'faq' => [
                        [
                            'question' => 'Apa itu reseller hosting?',
                            'answer' => 'Reseller hosting memungkinkan Anda membeli resource server dalam jumlah besar lalu membaginya menjadi beberapa akun hosting untuk dijual atau diberikan kepada klien.',
                        ],
                        [
                            'question' => 'Berapa akun yang bisa dibuat?',
                            'answer' => 'Tergantung paket: RackNerd 15–50 akun, HostBrr 10 akun pada paket 50 GB, sedangkan NameCrane menyediakan 20–160 akun cPanel atau 25–500 sub-akun DirectAdmin.',
                        ],
                        [
                            'question' => 'Apakah panelnya bisa white-label?',
                            'answer' => 'RackNerd mencantumkan white-label hosting, NameCrane menyediakan white-label cPanel/DirectAdmin dan vanity nameserver, sedangkan HostBrr menyediakan WHM untuk mengelola akun klien.',
                        ],
                        [
                            'question' => 'Apakah harga sudah termasuk lisensi billing?',
                            'answer' => 'RackNerd menyertakan Clientexec, NameCrane menyertakan Blesta dan menjual WHMCS dengan diskon. Cek detail masa berlaku lisensi saat checkout.',
                        ],
                    ],
                ],
                'en' => [
                    'intro' => "Reseller hosting lets you build your own hosting service: buy a larger plan, then split it into accounts for clients. This category covers RackNerd with the AdminBolt panel and a Clientexec license, NameCrane cPanel and DirectAdmin reseller plans with WHM/DirectAdmin reseller access and a free Blesta license, and HostBrr cPanel reseller plans with add-on accounts.\n\nPlans differ by total NVMe storage, the number of accounts you can create, per-account CPU/RAM limits, and white-label features. RackNerd offers 15–50 accounts, HostBrr includes 10 accounts on the 50 GB plan, while NameCrane offers 20–160 cPanel accounts or 25–500 DirectAdmin sub-accounts depending on the plan.\n\nPay attention to the billing period, overselling policy, and billing software license. RackNerd promo pricing is billed annually, while HostBrr and NameCrane are billed monthly with annual options available.",
                    'meta_title' => 'Affordable Reseller Hosting: Pricing and Plan Comparison',
                    'meta_description' => 'Compare reseller hosting from RackNerd, NameCrane, and HostBrr: NVMe storage, account limits, white-label panels, per-account resources, and billing software licenses.',
                    'faq' => [
                        [
                            'question' => 'What is reseller hosting?',
                            'answer' => 'Reseller hosting lets you buy server resources in bulk and split them into multiple hosting accounts to sell or provide to clients.',
                        ],
                        [
                            'question' => 'How many accounts can I create?',
                            'answer' => 'It depends on the plan: RackNerd offers 15–50 accounts, HostBrr includes 10 accounts on the 50 GB plan, while NameCrane offers 20–160 cPanel accounts or 25–500 DirectAdmin sub-accounts.',
                        ],
                        [
                            'question' => 'Is the panel white-label?',
                            'answer' => 'RackNerd lists white-label hosting, NameCrane provides white-label cPanel/DirectAdmin and vanity nameservers, while HostBrr provides WHM to manage client accounts.',
                        ],
                        [
                            'question' => 'Does the price include a billing software license?',
                            'answer' => 'RackNerd includes Clientexec, while NameCrane includes Blesta and sells WHMCS at a discount. Check the license validity details at checkout.',
                        ],
                    ],
                ],
            ],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Database\Seeder;

class CryptoProThemeSettingsSeeder extends Seeder
{
    public function run(?Theme $theme = null): void
    {
        $theme ??= Theme::query()->where('slug', 'cryptopro')->first();

        if ($theme === null) {
            return;
        }

        $this->seedHero($theme);
        $this->seedBranding($theme);
        $this->seedStats($theme);
        $this->seedAbout($theme);
        $this->seedAboutPage($theme);
        $this->seedSectionTitles($theme);
        $this->seedProcess($theme);
        $this->seedPricing($theme);
        $this->seedFaq($theme);
        $this->seedBlog($theme);
        $this->seedCta($theme);
        $this->seedContact($theme);
        $this->seedNewsletter($theme);
        $this->seedFooter($theme);

        Theme::clearCache();
    }

    protected function seedHero(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'hero', 'title', [
            'id' => 'Konsultasi Blockchain & Investasi Kripto',
            'en' => 'Blockchain Consultancy and Cryptocurrency Investments',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'subtitle', [
            'id' => 'Proyek open-source tanpa otoritas pusat — transaksi transparan sepenuhnya on-chain.',
            'en' => 'Open-source projects with no central authority — transactions conducted entirely on the blockchain.',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_label', [
            'id' => 'Mulai Sekarang',
            'en' => 'Get Started',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_url', 'https://cryptopro.demo/id/contact');

        ThemeSetting::set($theme->id, 'hero', 'features', [
            [
                'icon' => '₿',
                'title' => [
                    'id' => 'Aset kripto populer global',
                    'en' => 'The world’s most popular cryptocurrency',
                ],
                'description' => [
                    'id' => 'Edukasi dan strategi investasi yang aman.',
                    'en' => 'Education and safer investment strategies.',
                ],
            ],
            [
                'icon' => '🔐',
                'title' => [
                    'id' => 'Keamanan & kepemilikan pribadi',
                    'en' => 'Security, privately owned, on-chain',
                ],
                'description' => [
                    'id' => 'Wallet non-custodial dan praktik terbaik.',
                    'en' => 'Non-custodial wallets and best practices.',
                ],
            ],
            [
                'icon' => '💱',
                'title' => [
                    'id' => 'Transaksi finansial kripto',
                    'en' => 'Cryptocurrency for financial transactions',
                ],
                'description' => [
                    'id' => 'Integrasi payment & treasury untuk bisnis.',
                    'en' => 'Payment and treasury integration for business.',
                ],
            ],
        ]);
    }

    protected function seedBranding(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'branding', 'primary_color', '#eab308');
        ThemeSetting::set($theme->id, 'branding', 'secondary_color', '#312e81');
        ThemeSetting::set($theme->id, 'branding', 'accent_color', '#06b6d4');
        ThemeSetting::set($theme->id, 'branding', 'font_heading', 'Montserrat');
        ThemeSetting::set($theme->id, 'branding', 'font_body', 'Inter');
    }

    protected function seedStats(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'stats', 'items', [
            [
                'number' => '99.9',
                'suffix' => '%',
                'label' => ['id' => 'Uptime node', 'en' => 'Node uptime'],
            ],
            [
                'number' => '150',
                'suffix' => '+',
                'label' => ['id' => 'Wallet terkelola', 'en' => 'Wallets managed'],
            ],
            [
                'number' => '12',
                'suffix' => '',
                'label' => ['id' => 'Chain didukung', 'en' => 'Chains supported'],
            ],
            [
                'number' => '24',
                'suffix' => '/7',
                'label' => ['id' => 'Dukungan', 'en' => 'Support'],
            ],
        ]);
    }

    protected function seedAbout(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'about', 'title', [
            'id' => 'Blockchain tanpa otoritas pusat',
            'en' => 'Open-source blockchain, no central authority',
        ]);

        ThemeSetting::set($theme->id, 'about', 'subtitle', [
            'id' => 'Sembunyikan pesan Anda — privasi hanya satu klik.',
            'en' => 'Hide your messages in plain sight — privacy is only a click away.',
        ]);

        ThemeSetting::set($theme->id, 'about', 'content', [
            'id' => '<p>Kriptografi mencegah pihak ketiga mengakses data pesan pribadi selama komunikasi. Kami membantu tim Anda merancang arsitektur wallet, smart contract audit, dan compliance lokal.</p>',
            'en' => '<p>Cryptography helps prevent third parties from accessing private message data during communication. We help teams design wallet architecture, smart contract audits, and local compliance.</p>',
        ]);

        ThemeSetting::set($theme->id, 'about', 'points', [
            ['text' => ['id' => 'Menghentikan pencurian data komunikasi', 'en' => 'Stops communication data theft']],
            ['text' => ['id' => 'Transaksi mudah & aman', 'en' => 'Easy, secure transactions']],
            ['text' => ['id' => 'Sistem wallet terintegrasi', 'en' => 'Integrated wallet system']],
        ]);
    }

    protected function seedAboutPage(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'about_page', 'vision', [
            'id' => 'Membuat adopsi blockchain aman dan praktis di Indonesia.',
            'en' => 'Make blockchain adoption safe and practical in emerging markets.',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'mission', [
            'id' => 'Konsultasi, implementasi, dan edukasi Web3 untuk bisnis.',
            'en' => 'Consulting, implementation, and Web3 education for business.',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'history', [
            'id' => '<p>CryptoPro lahir dari tim engineer dan analyst yang fokus pada DeFi, custody, dan regulasi. Kami menggabungkan pengalaman exchange, audit kontrak, dan produk SaaS.</p>',
            'en' => '<p>CryptoPro was founded by engineers and analysts focused on DeFi, custody, and regulation — combining exchange, contract audit, and SaaS product experience.</p>',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'values', [
            [
                'title' => ['id' => 'Experience', 'en' => 'Experience'],
                'text' => ['id' => 'Track record implementasi mainnet & testnet.', 'en' => 'Proven mainnet and testnet delivery.'],
            ],
            [
                'title' => ['id' => 'Reliability', 'en' => 'Reliability'],
                'text' => ['id' => 'Monitoring 24/7 dan playbook insiden.', 'en' => '24/7 monitoring and incident playbooks.'],
            ],
            [
                'title' => ['id' => 'Security', 'en' => 'Security'],
                'text' => ['id' => 'Audit, multisig, dan cold storage.', 'en' => 'Audits, multisig, and cold storage.'],
            ],
        ]);
    }

    protected function seedSectionTitles(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'services', 'title', [
            'id' => 'Mengapa memilih kami',
            'en' => 'Why choose us',
        ]);
        ThemeSetting::set($theme->id, 'services', 'subtitle', [
            'id' => 'Secure · Easy transactions · Wallet system · 24/7 Support',
            'en' => 'Secure · Easy transactions · Wallet system · 24/7 Support',
        ]);
        ThemeSetting::set($theme->id, 'services', 'cta_label', [
            'id' => 'Lihat Layanan',
            'en' => 'View Services',
        ]);

        ThemeSetting::set($theme->id, 'portfolio', 'title', [
            'id' => 'Studi kasus',
            'en' => 'Case studies',
        ]);
        ThemeSetting::set($theme->id, 'portfolio', 'subtitle', [
            'id' => 'Implementasi nyata di fintech dan Web3.',
            'en' => 'Real implementations in fintech and Web3.',
        ]);
        ThemeSetting::set($theme->id, 'portfolio', 'cta_label', [
            'id' => 'Semua studi kasus',
            'en' => 'All case studies',
        ]);

        ThemeSetting::set($theme->id, 'team', 'title', [
            'id' => 'Tim ahli',
            'en' => 'Expert team',
        ]);
        ThemeSetting::set($theme->id, 'team', 'subtitle', [
            'id' => 'Analyst, smart contract engineer, dan compliance.',
            'en' => 'Analysts, smart contract engineers, and compliance.',
        ]);

        ThemeSetting::set($theme->id, 'testimonial', 'title', [
            'id' => 'Dipercaya klien',
            'en' => 'Trusted by clients',
        ]);
        ThemeSetting::set($theme->id, 'testimonial', 'subtitle', [
            'id' => 'Feedback dari partner exchange dan startup DeFi.',
            'en' => 'Feedback from exchange partners and DeFi startups.',
        ]);
    }

    protected function seedProcess(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'process', 'title', [
            'id' => 'Mudah memulai',
            'en' => 'Easy to get started',
        ]);
        ThemeSetting::set($theme->id, 'process', 'subtitle', [
            'id' => 'Empat langkah dari registrasi hingga trading aman.',
            'en' => 'Four steps from registration to secure trading.',
        ]);

        ThemeSetting::set($theme->id, 'process', 'steps', [
            [
                'number' => '01',
                'title' => ['id' => 'Register', 'en' => 'Register'],
                'description' => ['id' => 'Buat akun dan verifikasi identitas (KYC).', 'en' => 'Create an account and complete KYC.'],
            ],
            [
                'number' => '02',
                'title' => ['id' => 'Fund wallet', 'en' => 'Fund wallet'],
                'description' => ['id' => 'Deposit fiat atau kripto ke wallet custodial/non-custodial.', 'en' => 'Deposit fiat or crypto to your wallet.'],
            ],
            [
                'number' => '03',
                'title' => ['id' => 'Start trading', 'en' => 'Start trading'],
                'description' => ['id' => 'Akses dashboard dan strategi yang disetujui.', 'en' => 'Access the dashboard and approved strategies.'],
            ],
            [
                'number' => '04',
                'title' => ['id' => 'Monitor & support', 'en' => 'Monitor & support'],
                'description' => ['id' => 'Alert, laporan, dan dukungan 24/7.', 'en' => 'Alerts, reporting, and 24/7 support.'],
            ],
        ]);
    }

    protected function seedPricing(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'pricing', 'title', [
            'id' => 'Paket konsultasi',
            'en' => 'Consulting plans',
        ]);
        ThemeSetting::set($theme->id, 'pricing', 'subtitle', [
            'id' => 'Dari edukasi tim hingga audit smart contract penuh.',
            'en' => 'From team education to full smart contract audits.',
        ]);

        ThemeSetting::set($theme->id, 'pricing', 'plans', [
            [
                'name' => ['id' => 'Starter', 'en' => 'Starter'],
                'price' => '$499',
                'period' => 'bulan',
                'is_popular' => false,
                'cta_label' => ['id' => 'Pilih Starter', 'en' => 'Choose Starter'],
                'cta_url' => 'https://cryptopro.demo/id/contact',
                'features' => [
                    ['text' => ['id' => 'Workshop blockchain 101', 'en' => 'Blockchain 101 workshop']],
                    ['text' => ['id' => 'Review wallet setup', 'en' => 'Wallet setup review']],
                ],
            ],
            [
                'name' => ['id' => 'Pro', 'en' => 'Pro'],
                'price' => '$1,499',
                'period' => 'bulan',
                'is_popular' => true,
                'cta_label' => ['id' => 'Pilih Pro', 'en' => 'Choose Pro'],
                'cta_url' => 'https://cryptopro.demo/id/contact',
                'features' => [
                    ['text' => ['id' => 'Audit kontrak (scope menengah)', 'en' => 'Mid-scope contract audit']],
                    ['text' => ['id' => 'Integrasi payment kripto', 'en' => 'Crypto payment integration']],
                ],
            ],
            [
                'name' => ['id' => 'Enterprise', 'en' => 'Enterprise'],
                'price' => 'Custom',
                'period' => '',
                'is_popular' => false,
                'cta_label' => ['id' => 'Hubungi kami', 'en' => 'Contact us'],
                'cta_url' => 'https://cryptopro.demo/id/contact',
                'features' => [
                    ['text' => ['id' => 'Tim dedicated & SLA', 'en' => 'Dedicated team & SLA']],
                    ['text' => ['id' => 'Compliance & custody', 'en' => 'Compliance & custody']],
                ],
            ],
        ]);
    }

    protected function seedFaq(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'faq', 'title', [
            'id' => 'FAQ',
            'en' => 'FAQ',
        ]);
        ThemeSetting::set($theme->id, 'faq', 'subtitle', [
            'id' => 'Pertanyaan umum seputar wallet dan regulasi.',
            'en' => 'Common questions about wallets and regulation.',
        ]);

        ThemeSetting::set($theme->id, 'faq', 'items', [
            [
                'question' => ['id' => 'Apakah layanan ini memberi saran investasi?', 'en' => 'Is this investment advice?'],
                'answer' => ['id' => 'Kami konsultasi teknologi dan risiko; keputusan investasi tetap di tangan Anda.', 'en' => 'We consult on technology and risk; investment decisions remain yours.'],
            ],
            [
                'question' => ['id' => 'Wallet custodial atau non-custodial?', 'en' => 'Custodial or non-custodial?'],
                'answer' => ['id' => 'Keduanya didukung sesuai kebutuhan compliance klien.', 'en' => 'Both supported depending on client compliance needs.'],
            ],
            [
                'question' => ['id' => 'Chain apa saja yang didukung?', 'en' => 'Which chains are supported?'],
                'answer' => ['id' => 'EVM utama, Bitcoin, dan L2 populer — hubungi untuk daftar lengkap.', 'en' => 'Major EVM, Bitcoin, and popular L2s — contact us for the full list.'],
            ],
        ]);
    }

    protected function seedBlog(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'blog', 'title', [
            'id' => 'Insight Web3',
            'en' => 'Web3 insights',
        ]);
        ThemeSetting::set($theme->id, 'blog', 'subtitle', [
            'id' => 'Analisis market, keamanan, dan regulasi.',
            'en' => 'Market, security, and regulation analysis.',
        ]);
    }

    protected function seedCta(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'cta', 'title', [
            'id' => 'Siap masuk ke ekosistem blockchain?',
            'en' => 'Ready to enter the blockchain ecosystem?',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'subtitle', [
            'id' => 'Jadwalkan sesi konsultasi gratis dengan tim kami.',
            'en' => 'Book a free consultation with our team.',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_label', [
            'id' => 'Hubungi Kami',
            'en' => 'Contact Us',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_url', 'https://cryptopro.demo/id/contact');
    }

    protected function seedContact(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'contact', 'address', 'Crypto Tower, Level 12, Jakarta');
        ThemeSetting::set($theme->id, 'contact', 'phone', '+62 812-0000-CRYPTO');
        ThemeSetting::set($theme->id, 'contact', 'email', 'hello@cryptopro.demo');
        ThemeSetting::set($theme->id, 'contact', 'working_hours', '24/7 desk · Senin–Jumat priority support');
        ThemeSetting::set($theme->id, 'contact', 'map_embed', '');
    }

    protected function seedNewsletter(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'newsletter', 'title', [
            'id' => 'Alpha report mingguan',
            'en' => 'Weekly alpha report',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'subtitle', [
            'id' => 'Ringkasan on-chain & regulasi ke inbox Anda.',
            'en' => 'On-chain and regulation summaries in your inbox.',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'placeholder', [
            'id' => 'Email Anda',
            'en' => 'Your email',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'button_label', [
            'id' => 'Berlangganan',
            'en' => 'Subscribe',
        ]);
    }

    protected function seedFooter(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'footer', 'about_text', [
            'id' => 'CryptoPro — konsultasi blockchain, wallet, dan strategi kripto untuk bisnis.',
            'en' => 'CryptoPro — blockchain consulting, wallets, and crypto strategy for business.',
        ]);

        ThemeSetting::set($theme->id, 'footer', 'copyright', [
            'id' => 'Copyright © :year :site_name. Semua hak dilindungi.',
            'en' => 'Copyright © :year :site_name. All Rights Reserved',
        ]);

        ThemeSetting::set($theme->id, 'footer', 'columns', [
            [
                'title' => ['id' => 'Perusahaan', 'en' => 'Company'],
                'menu_location' => 'footer-company',
            ],
            [
                'title' => ['id' => 'Layanan', 'en' => 'Services'],
                'menu_location' => 'footer-services',
            ],
            [
                'title' => ['id' => 'Legal', 'en' => 'Legal'],
                'menu_location' => 'footer-legal',
            ],
        ]);
    }
}

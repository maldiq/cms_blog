<?php

namespace Database\Seeders;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Database\Seeder;

class PurpleLandingThemeSettingsSeeder extends Seeder
{
    public function run(?Theme $theme = null): void
    {
        $theme ??= Theme::query()->where('slug', 'purplelanding')->first();

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
            'id' => 'Bisnis Lebih Baik dengan Solusi Kami',
            'en' => 'Business With Our Solutions',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'subtitle', [
            'id' => 'Tingkatkan efisiensi, produktivitas, dan performa — kontribusi nyata pada kesuksesan bisnis Anda.',
            'en' => 'Enhance efficiency, productivity, and overall performance — contributing to your business success and sustainability.',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'lead_form_title', [
            'id' => 'Ceritakan tentang bisnis Anda',
            'en' => 'Tell Us About Your Business',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_label', [
            'id' => 'Let\'s Talk',
            'en' => 'Let\'s Talk',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_url', '#contact');

        ThemeSetting::set($theme->id, 'hero', 'features', []);
    }

    protected function seedBranding(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'branding', 'primary_color', '#7c3aed');
        ThemeSetting::set($theme->id, 'branding', 'secondary_color', '#5b21b6');
        ThemeSetting::set($theme->id, 'branding', 'accent_color', '#c084fc');
        ThemeSetting::set($theme->id, 'branding', 'font_heading', 'Montserrat');
        ThemeSetting::set($theme->id, 'branding', 'font_body', 'Inter');
    }

    protected function seedStats(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'stats', 'items', [
            [
                'number' => '1',
                'suffix' => 'B+',
                'label' => ['id' => 'Pendapatan tahunan', 'en' => 'We earning a year'],
            ],
            [
                'number' => '4',
                'suffix' => 'M+',
                'label' => ['id' => 'Klien', 'en' => 'Awesome clients'],
            ],
            [
                'number' => '95',
                'suffix' => '%',
                'label' => ['id' => 'Klien puas', 'en' => 'Satisfied clients'],
            ],
            [
                'number' => '10',
                'suffix' => '+',
                'label' => ['id' => 'Tahun pengalaman', 'en' => 'Years of experience'],
            ],
        ]);
    }

    protected function seedAbout(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'about', 'title', [
            'id' => 'Manfaat untuk bisnis Anda',
            'en' => 'Our benefits for your business',
        ]);

        ThemeSetting::set($theme->id, 'about', 'subtitle', [
            'id' => 'Fitur robust, analytics bernilai, dan dukungan responsif.',
            'en' => 'Robust features, valuable analytics, and responsive support.',
        ]);

        ThemeSetting::set($theme->id, 'about', 'content', [
            'id' => '<p>Solusi kami dirancang khusus untuk kebutuhan bisnis Anda — kustomisasi mulus dan skalabilitas. Tim support kami berkomitmen pada kepuasan klien.</p>',
            'en' => '<p>Our solution is tailored to your business needs with seamless customization and scalability. Analytics empower your decisions, and our support team addresses inquiries promptly.</p>',
        ]);

        ThemeSetting::set($theme->id, 'about', 'points', [
            ['text' => ['id' => 'Desain unik & kode berkualitas', 'en' => 'Unique design & quality code']],
            ['text' => ['id' => 'Dukungan premium', 'en' => 'Premium support']],
            ['text' => ['id' => '10+ tahun pengalaman bisnis', 'en' => '10+ years of business experience']],
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
            'id' => 'Solusi Kami',
            'en' => 'Our Solutions',
        ]);
        ThemeSetting::set($theme->id, 'services', 'subtitle', [
            'id' => 'Meningkatkan kesuksesan bisnis Anda',
            'en' => 'Elevating your business success',
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
            'id' => 'Harga & Paket',
            'en' => 'Pricing & Plans',
        ]);
        ThemeSetting::set($theme->id, 'pricing', 'subtitle', [
            'id' => 'Harga dan paket sempurna untuk Anda',
            'en' => 'We deliver perfect pricing and plan just for you',
        ]);

        ThemeSetting::set($theme->id, 'pricing', 'plans', [
            [
                'name' => ['id' => 'Basic Plan', 'en' => 'Basic Plan'],
                'price' => '$39',
                'period' => 'month',
                'is_popular' => false,
                'cta_label' => ['id' => 'Choose Plan', 'en' => 'Choose Plan'],
                'cta_url' => '#contact',
                'features' => [
                    ['text' => ['id' => 'Customer Management', 'en' => 'Customer Management']],
                    ['text' => ['id' => 'Mobile Apps', 'en' => 'Mobile Apps']],
                    ['text' => ['id' => 'Email Support', 'en' => 'Email Support']],
                ],
            ],
            [
                'name' => ['id' => 'Business Plan', 'en' => 'Business Plan'],
                'price' => '$60',
                'period' => 'month',
                'is_popular' => true,
                'cta_label' => ['id' => 'Choose Plan', 'en' => 'Choose Plan'],
                'cta_url' => '#contact',
                'features' => [
                    ['text' => ['id' => 'Browser extension', 'en' => 'Browser extension']],
                    ['text' => ['id' => 'Calendar integration', 'en' => 'Calendar integration']],
                    ['text' => ['id' => 'Premium support', 'en' => 'Premium support']],
                ],
            ],
            [
                'name' => ['id' => 'Enterprise Plan', 'en' => 'Enterprise Plan'],
                'price' => '$79',
                'period' => 'month',
                'is_popular' => false,
                'cta_label' => ['id' => 'Choose Plan', 'en' => 'Choose Plan'],
                'cta_url' => '#contact',
                'features' => [
                    ['text' => ['id' => 'Semua fitur Business', 'en' => 'All Business features']],
                    ['text' => ['id' => 'SLA dedicated', 'en' => 'Dedicated SLA']],
                    ['text' => ['id' => 'Onboarding tim', 'en' => 'Team onboarding']],
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
            'id' => 'Kami siap membantu menangani tugas harian Anda',
            'en' => 'We aim to assist you in handling daily tasks',
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
            'id' => 'Kami punya ide bisnis untuk Anda',
            'en' => 'We have business ideas',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'subtitle', [
            'id' => 'Mari wujudkan solusi digital berikutnya bersama tim kami.',
            'en' => 'Let’s build your next digital solution with our team.',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_label', [
            'id' => 'Let\'s Talk',
            'en' => 'Let\'s Talk',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_url', '#contact');
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
            'id' => 'Purple Landing — teknologi inovatif untuk kenyamanan dan pertumbuhan bisnis Anda.',
            'en' => 'Welcome to the future with innovative technologies that transform your everyday business.',
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

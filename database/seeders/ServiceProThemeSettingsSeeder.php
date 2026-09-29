<?php

namespace Database\Seeders;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Database\Seeder;

class ServiceProThemeSettingsSeeder extends Seeder
{
    public function run(?Theme $theme = null): void
    {
        $theme ??= Theme::query()->where('slug', 'servicepro')->first();

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
            'id' => 'Solusi Profesional untuk Bisnis Anda',
            'en' => 'Professional Solutions for Your Business',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'subtitle', [
            'id' => 'Kami membantu brand tumbuh dengan layanan digital, infrastruktur, dan dukungan teknis terpercaya.',
            'en' => 'We help brands grow with trusted digital services, infrastructure, and technical support.',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_label', [
            'id' => 'Konsultasi Gratis',
            'en' => 'Free Consultation',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'cta_url', '/id/contact');

        ThemeSetting::set($theme->id, 'hero', 'features', [
            [
                'icon' => '⚡',
                'title' => [
                    'id' => 'Respon Cepat',
                    'en' => 'Fast Response',
                ],
                'description' => [
                    'id' => 'Tim support siap membantu dalam waktu singkat.',
                    'en' => 'Support team ready to help in no time.',
                ],
            ],
            [
                'icon' => '🛡️',
                'title' => [
                    'id' => 'Aman & Andal',
                    'en' => 'Secure & Reliable',
                ],
                'description' => [
                    'id' => 'Praktik terbaik keamanan dan backup rutin.',
                    'en' => 'Security best practices and regular backups.',
                ],
            ],
            [
                'icon' => '📈',
                'title' => [
                    'id' => 'Hasil Terukur',
                    'en' => 'Measurable Results',
                ],
                'description' => [
                    'id' => 'Laporan progress dan KPI yang transparan.',
                    'en' => 'Transparent progress reports and KPIs.',
                ],
            ],
        ]);
    }

    protected function seedBranding(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'branding', 'primary_color', '#0f766e');
        ThemeSetting::set($theme->id, 'branding', 'secondary_color', '#134e4a');
        ThemeSetting::set($theme->id, 'branding', 'accent_color', '#f59e0b');
        ThemeSetting::set($theme->id, 'branding', 'font_heading', 'Montserrat');
        ThemeSetting::set($theme->id, 'branding', 'font_body', 'Inter');
    }

    protected function seedStats(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'stats', 'items', [
            [
                'number' => '120',
                'suffix' => '+',
                'label' => ['id' => 'Proyek Selesai', 'en' => 'Projects Done'],
            ],
            [
                'number' => '85',
                'suffix' => '+',
                'label' => ['id' => 'Klien Aktif', 'en' => 'Active Clients'],
            ],
            [
                'number' => '8',
                'suffix' => '',
                'label' => ['id' => 'Tahun Pengalaman', 'en' => 'Years Experience'],
            ],
            [
                'number' => '24',
                'suffix' => '/7',
                'label' => ['id' => 'Dukungan Teknis', 'en' => 'Technical Support'],
            ],
        ]);
    }

    protected function seedAbout(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'about', 'title', [
            'id' => 'Tentang ServicePro',
            'en' => 'About ServicePro',
        ]);

        ThemeSetting::set($theme->id, 'about', 'subtitle', [
            'id' => 'Partner teknologi untuk UMKM hingga enterprise.',
            'en' => 'Technology partner for SMEs to enterprise.',
        ]);

        ThemeSetting::set($theme->id, 'about', 'content', [
            'id' => '<p>Kami adalah tim konsultan dan engineer yang fokus pada solusi praktis: website, server, backup, dan maintenance. Setiap proyek diawali dengan analisis kebutuhan agar investasi teknologi Anda tepat sasaran.</p>',
            'en' => '<p>We are consultants and engineers focused on practical solutions: websites, servers, backup, and maintenance. Every project starts with needs analysis so your technology investment hits the mark.</p>',
        ]);

        ThemeSetting::set($theme->id, 'about', 'points', [
            ['text' => ['id' => 'Tim berpengalaman di Laravel & cloud', 'en' => 'Experienced Laravel & cloud team']],
            ['text' => ['id' => 'Proses transparan dari awal hingga go-live', 'en' => 'Transparent process from kickoff to go-live']],
            ['text' => ['id' => 'Garansi maintenance pasca peluncuran', 'en' => 'Post-launch maintenance guarantee']],
        ]);
    }

    protected function seedAboutPage(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'about_page', 'vision', [
            'id' => 'Menjadi partner digital terpercaya di Indonesia.',
            'en' => 'To become a trusted digital partner in Indonesia.',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'mission', [
            'id' => 'Memberi solusi teknologi yang berdampak, aman, dan mudah dirawat.',
            'en' => 'Deliver impactful, secure, and maintainable technology solutions.',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'history', [
            'id' => '<p>ServicePro dimulai sebagai layanan jasa IT lokal pada 2018. Seiring permintaan klien, kami berkembang menjadi agency full-stack: development, DevOps, dan managed support.</p>',
            'en' => '<p>ServicePro started as a local IT services shop in 2018. As client demand grew, we became a full-stack agency: development, DevOps, and managed support.</p>',
        ]);

        ThemeSetting::set($theme->id, 'about_page', 'values', [
            [
                'title' => ['id' => 'Integritas', 'en' => 'Integrity'],
                'text' => ['id' => 'Transparan, jujur, dan bertanggung jawab.', 'en' => 'Transparent, honest, and accountable.'],
            ],
            [
                'title' => ['id' => 'Kualitas', 'en' => 'Quality'],
                'text' => ['id' => 'Kode rapi, dokumentasi jelas, testing rutin.', 'en' => 'Clean code, clear docs, regular testing.'],
            ],
            [
                'title' => ['id' => 'Kolaborasi', 'en' => 'Collaboration'],
                'text' => ['id' => 'Bekerja sama dengan tim klien, bukan menggantikan.', 'en' => 'Work with client teams, not replace them.'],
            ],
        ]);
    }

    protected function seedSectionTitles(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'services', 'title', [
            'id' => 'Layanan Kami',
            'en' => 'Our Services',
        ]);
        ThemeSetting::set($theme->id, 'services', 'subtitle', [
            'id' => 'Paket fleksibel untuk kebutuhan bisnis Anda.',
            'en' => 'Flexible packages for your business needs.',
        ]);
        ThemeSetting::set($theme->id, 'services', 'cta_label', [
            'id' => 'Lihat Semua Layanan',
            'en' => 'View All Services',
        ]);

        ThemeSetting::set($theme->id, 'portfolio', 'title', [
            'id' => 'Portfolio Terpilih',
            'en' => 'Featured Portfolio',
        ]);
        ThemeSetting::set($theme->id, 'portfolio', 'subtitle', [
            'id' => 'Beberapa proyek yang kami bangun bersama klien.',
            'en' => 'Selected projects we built with clients.',
        ]);
        ThemeSetting::set($theme->id, 'portfolio', 'cta_label', [
            'id' => 'Jelajahi Portfolio',
            'en' => 'Explore Portfolio',
        ]);

        ThemeSetting::set($theme->id, 'team', 'title', [
            'id' => 'Tim Kami',
            'en' => 'Our Team',
        ]);
        ThemeSetting::set($theme->id, 'team', 'subtitle', [
            'id' => 'Orang-orang di balik layanan ServicePro.',
            'en' => 'The people behind ServicePro.',
        ]);

        ThemeSetting::set($theme->id, 'testimonial', 'title', [
            'id' => 'Apa Kata Klien',
            'en' => 'What Clients Say',
        ]);
        ThemeSetting::set($theme->id, 'testimonial', 'subtitle', [
            'id' => 'Testimoni dari partner yang sudah bekerja sama.',
            'en' => 'Feedback from partners we have worked with.',
        ]);
    }

    protected function seedProcess(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'process', 'title', [
            'id' => 'Alur Kerja',
            'en' => 'How We Work',
        ]);
        ThemeSetting::set($theme->id, 'process', 'subtitle', [
            'id' => 'Proses sederhana, hasil maksimal.',
            'en' => 'Simple process, maximum results.',
        ]);

        ThemeSetting::set($theme->id, 'process', 'steps', [
            [
                'number' => '01',
                'title' => ['id' => 'Discovery', 'en' => 'Discovery'],
                'description' => ['id' => 'Workshop kebutuhan & prioritas.', 'en' => 'Needs workshop and priorities.'],
            ],
            [
                'number' => '02',
                'title' => ['id' => 'Perancangan', 'en' => 'Design'],
                'description' => ['id' => 'Wireframe, arsitektur, dan estimasi.', 'en' => 'Wireframes, architecture, estimates.'],
            ],
            [
                'number' => '03',
                'title' => ['id' => 'Implementasi', 'en' => 'Implementation'],
                'description' => ['id' => 'Development iteratif dengan demo rutin.', 'en' => 'Iterative development with regular demos.'],
            ],
            [
                'number' => '04',
                'title' => ['id' => 'Go-live & Support', 'en' => 'Go-live & Support'],
                'description' => ['id' => 'Deploy, monitoring, dan maintenance.', 'en' => 'Deploy, monitoring, and maintenance.'],
            ],
        ]);
    }

    protected function seedPricing(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'pricing', 'title', [
            'id' => 'Paket Harga',
            'en' => 'Pricing Plans',
        ]);
        ThemeSetting::set($theme->id, 'pricing', 'subtitle', [
            'id' => 'Pilih paket yang sesuai skala bisnis Anda.',
            'en' => 'Choose a plan that fits your business scale.',
        ]);

        ThemeSetting::set($theme->id, 'pricing', 'plans', [
            [
                'name' => ['id' => 'Starter', 'en' => 'Starter'],
                'price' => 'Rp 2.500.000',
                'period' => 'proyek',
                'is_popular' => false,
                'cta_label' => ['id' => 'Mulai Starter', 'en' => 'Get Starter'],
                'cta_url' => '/id/contact',
                'features' => [
                    ['text' => ['id' => 'Landing page responsif', 'en' => 'Responsive landing page']],
                    ['text' => ['id' => 'Form kontak & SEO dasar', 'en' => 'Contact form & basic SEO']],
                    ['text' => ['id' => '1 bulan support', 'en' => '1 month support']],
                ],
            ],
            [
                'name' => ['id' => 'Business', 'en' => 'Business'],
                'price' => 'Rp 7.500.000',
                'period' => 'proyek',
                'is_popular' => true,
                'cta_label' => ['id' => 'Pilih Business', 'en' => 'Choose Business'],
                'cta_url' => '/id/contact',
                'features' => [
                    ['text' => ['id' => 'Website multi-halaman + CMS', 'en' => 'Multi-page site + CMS']],
                    ['text' => ['id' => 'Integrasi analytics', 'en' => 'Analytics integration']],
                    ['text' => ['id' => '3 bulan maintenance', 'en' => '3 months maintenance']],
                ],
            ],
            [
                'name' => ['id' => 'Enterprise', 'en' => 'Enterprise'],
                'price' => 'Custom',
                'period' => '',
                'is_popular' => false,
                'cta_label' => ['id' => 'Hubungi Sales', 'en' => 'Contact Sales'],
                'cta_url' => '/id/contact',
                'features' => [
                    ['text' => ['id' => 'Arsitektur skala besar', 'en' => 'Large-scale architecture']],
                    ['text' => ['id' => 'SLA & DevOps dedicated', 'en' => 'SLA & dedicated DevOps']],
                    ['text' => ['id' => 'Tim on-call', 'en' => 'On-call team']],
                ],
            ],
        ]);
    }

    protected function seedFaq(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'faq', 'title', [
            'id' => 'Pertanyaan Umum',
            'en' => 'FAQ',
        ]);
        ThemeSetting::set($theme->id, 'faq', 'subtitle', [
            'id' => 'Jawaban singkat sebelum kita diskusi lebih lanjut.',
            'en' => 'Quick answers before we talk further.',
        ]);

        ThemeSetting::set($theme->id, 'faq', 'items', [
            [
                'question' => ['id' => 'Berapa lama pengerjaan website?', 'en' => 'How long does a website take?'],
                'answer' => ['id' => 'Landing page 2–3 minggu; website CMS 4–8 minggu tergantung scope.', 'en' => 'Landing pages 2–3 weeks; CMS sites 4–8 weeks depending on scope.'],
            ],
            [
                'question' => ['id' => 'Apakah source code diserahkan?', 'en' => 'Do you hand over source code?'],
                'answer' => ['id' => 'Ya, untuk proyek fixed-price kode menjadi milik klien setelah pelunasan.', 'en' => 'Yes, for fixed-price projects code belongs to the client after final payment.'],
            ],
            [
                'question' => ['id' => 'Apakah ada layanan maintenance?', 'en' => 'Do you offer maintenance?'],
                'answer' => ['id' => 'Tersedia paket bulanan backup, update, dan monitoring server.', 'en' => 'Monthly packages for backup, updates, and server monitoring.'],
            ],
        ]);
    }

    protected function seedBlog(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'blog', 'title', [
            'id' => 'Artikel Terbaru',
            'en' => 'Latest Articles',
        ]);

        ThemeSetting::set($theme->id, 'blog', 'subtitle', [
            'id' => 'Insight dan tips dari tim kami.',
            'en' => 'Insights and tips from our team.',
        ]);
    }

    protected function seedCta(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'cta', 'title', [
            'id' => 'Siap Memulai Proyek Berikutnya?',
            'en' => 'Ready for Your Next Project?',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'subtitle', [
            'id' => 'Ceritakan kebutuhan Anda — kami akan merespons dalam 1×24 jam kerja.',
            'en' => 'Tell us your needs — we respond within one business day.',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_label', [
            'id' => 'Hubungi Kami',
            'en' => 'Contact Us',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_url', '/id/contact');
    }

    protected function seedContact(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'contact', 'address', 'Jl. Teknologi No. 88, Jakarta Selatan 12730');
        ThemeSetting::set($theme->id, 'contact', 'phone', '+62 812-3456-7890');
        ThemeSetting::set($theme->id, 'contact', 'email', 'hello@servicepro.demo');
        ThemeSetting::set($theme->id, 'contact', 'working_hours', 'Senin–Jumat, 09:00–18:00 WIB');
        ThemeSetting::set($theme->id, 'contact', 'map_embed', '<iframe src="https://maps.google.com/maps?q=Jakarta&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="320" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>');
    }

    protected function seedNewsletter(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'newsletter', 'title', [
            'id' => 'Newsletter Mingguan',
            'en' => 'Weekly Newsletter',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'subtitle', [
            'id' => 'Tips teknologi dan update proyek langsung ke inbox Anda.',
            'en' => 'Tech tips and project updates straight to your inbox.',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'placeholder', [
            'id' => 'Alamat email Anda',
            'en' => 'Your email address',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'button_label', [
            'id' => 'Berlangganan',
            'en' => 'Subscribe',
        ]);
    }

    protected function seedFooter(Theme $theme): void
    {
        ThemeSetting::set($theme->id, 'footer', 'about_text', [
            'id' => 'ServicePro membantu bisnis tumbuh dengan layanan web, server, dan dukungan IT terpadu.',
            'en' => 'ServicePro helps businesses grow with web, server, and integrated IT support.',
        ]);

        ThemeSetting::set($theme->id, 'footer', 'copyright', [
            'id' => '© :year ServicePro Agency. Semua hak dilindungi.',
            'en' => '© :year ServicePro Agency. All rights reserved.',
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

<?php

namespace Database\Seeders;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Portfolio\Models\PortfolioCategory;
use App\Domain\Menu\Services\HeaderMenuFromContentService;
use App\Domain\Service\Services\SolusiwebServiceImportService;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\SocialSettings;
use App\Domain\Team\Models\Team;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Theme\Models\Theme;
use Illuminate\Database\Seeder;

class ServiceProDemoSeeder extends Seeder
{
    public function run(): void
    {
        $theme = $this->activateServiceProTheme();

        $this->call(ServiceProThemeSettingsSeeder::class, false, ['theme' => $theme]);

        $this->seedSiteSettings();
        try {
            app(SolusiwebServiceImportService::class)->import();
        } catch (\Throwable) {
            app(HeaderMenuFromContentService::class)->sync();
        }
        $this->seedFooterMenus();
        $this->seedPortfolios();
        $this->seedTeamMembers();
        $this->seedTestimonials();

        Theme::clearCache();
    }

    protected function activateServiceProTheme(): Theme
    {
        $manifestPath = resource_path('views/themes/servicepro/theme.json');
        $manifest = is_file($manifestPath)
            ? json_decode((string) file_get_contents($manifestPath), true)
            : [];

        Theme::query()->update(['is_active' => false]);

        $theme = Theme::query()->updateOrCreate(
            ['slug' => 'servicepro'],
            [
                'name' => $manifest['name'] ?? 'ServicePro',
                'description' => $manifest['description'] ?? null,
                'author' => $manifest['author'] ?? null,
                'version' => $manifest['version'] ?? '1.0.0',
                'preview_image' => $manifest['preview'] ?? null,
                'is_active' => true,
            ],
        );

        Theme::clearCache();

        return $theme;
    }

    protected function seedSiteSettings(): void
    {
        $general = app(GeneralSettings::class);
        $general->fill([
            'site_name' => $general->site_name ?: 'ServicePro Agency',
            'site_description' => $general->site_description ?: 'Agency layanan web & IT profesional.',
            'default_locale' => $general->default_locale ?: 'id',
            'timezone' => $general->timezone ?: 'Asia/Jakarta',
            'date_format' => $general->date_format ?: 'd M Y',
        ])->save();

        app(SocialSettings::class)->fill([
            'facebook' => 'https://facebook.com/servicepro.demo',
            'instagram' => 'https://instagram.com/servicepro.demo',
            'linkedin' => 'https://linkedin.com/company/servicepro-demo',
            'youtube' => 'https://youtube.com/@serviceprodemo',
        ])->save();
    }

    protected function seedFooterMenus(): void
    {
        $this->seedSimpleFooterMenu('footer-company', 'Footer Perusahaan', [
            ['id' => 'Tentang Kami', 'en' => 'About Us', 'url' => '/id/about'],
            ['id' => 'Tim', 'en' => 'Team', 'url' => '/id/team'],
            ['id' => 'Testimonial', 'en' => 'Testimonials', 'url' => '/id/testimonials'],
            ['id' => 'Kontak', 'en' => 'Contact', 'url' => '/id/contact'],
        ]);

        $this->seedSimpleFooterMenu('footer-services', 'Footer Layanan', [
            ['id' => 'Semua Layanan', 'en' => 'All Services', 'url' => '/id/services'],
            ['id' => 'Portfolio', 'en' => 'Portfolio', 'url' => '/id/portfolio'],
            ['id' => 'Harga', 'en' => 'Pricing', 'url' => '/id/pricing'],
            ['id' => 'FAQ', 'en' => 'FAQ', 'url' => '/id/faq'],
        ]);

        $this->seedSimpleFooterMenu('footer-legal', 'Footer Legal', [
            ['id' => 'Kebijakan Privasi', 'en' => 'Privacy Policy', 'url' => '/id/page/privacy-policy'],
            ['id' => 'Blog', 'en' => 'Blog', 'url' => '/id/blog'],
        ]);
    }

    /**
     * @param  list<array{id: string, en: string, url: string}>  $links
     */
    protected function seedSimpleFooterMenu(string $location, string $name, array $links): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => $location, 'is_active' => true],
            ['name' => $name],
        );

        MenuItem::query()->where('menu_id', $menu->id)->delete();

        foreach ($links as $index => $link) {
            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'parent_id' => null,
                'type' => MenuItem::TYPE_LINK,
                'target_id' => null,
                'url' => $link['url'],
                'label' => ['id' => $link['id'], 'en' => $link['en']],
                'icon' => null,
                'target' => '_self',
                'roles' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    protected function seedPortfolios(): void
    {
        if (Portfolio::query()->whereHas('translations', fn ($q) => $q->where('locale', 'id')->where('slug', 'portal-ecommerce-nusantara'))->exists()) {
            return;
        }

        $webCategory = PortfolioCategory::query()->create(['is_active' => true, 'sort_order' => 1]);
        $webCategory->translateOrNew('id')->fill([
            'name' => 'Website',
            'slug' => 'website',
        ])->save();
        $webCategory->translateOrNew('en')->fill([
            'name' => 'Websites',
            'slug' => 'websites',
        ])->save();

        $infraCategory = PortfolioCategory::query()->create(['is_active' => true, 'sort_order' => 2]);
        $infraCategory->translateOrNew('id')->fill([
            'name' => 'Infrastruktur',
            'slug' => 'infrastruktur',
        ])->save();
        $infraCategory->translateOrNew('en')->fill([
            'name' => 'Infrastructure',
            'slug' => 'infrastructure',
        ])->save();

        $projects = [
            [
                'sort_order' => 1,
                'category_id' => $webCategory->id,
                'client_name' => 'PT Nusantara Retail',
                'id' => [
                    'title' => 'Portal E-commerce Nusantara',
                    'slug' => 'portal-ecommerce-nusantara',
                    'excerpt' => 'Platform toko online multi-cabang dengan integrasi payment.',
                    'content' => '<p>Rebuild dari WordPress ke Laravel dengan performa 3× lebih cepat.</p>',
                ],
                'en' => [
                    'title' => 'Nusantara E-commerce Portal',
                    'slug' => 'nusantara-ecommerce-portal',
                    'excerpt' => 'Multi-branch online store with payment integration.',
                    'content' => '<p>Rebuilt from WordPress to Laravel with 3× faster performance.</p>',
                ],
            ],
            [
                'sort_order' => 2,
                'category_id' => $webCategory->id,
                'client_name' => 'Klinik Sehat Bersama',
                'id' => [
                    'title' => 'Website Klinik & Booking',
                    'slug' => 'website-klinik-booking',
                    'excerpt' => 'Company profile dengan formulir janji temu online.',
                    'content' => '<p>Desain ramah mobile dan integrasi WhatsApp notification.</p>',
                ],
                'en' => [
                    'title' => 'Clinic Website & Booking',
                    'slug' => 'clinic-website-booking',
                    'excerpt' => 'Company profile with online appointment forms.',
                    'content' => '<p>Mobile-friendly design and WhatsApp notifications.</p>',
                ],
            ],
            [
                'sort_order' => 3,
                'category_id' => $infraCategory->id,
                'client_name' => 'Logistik Express',
                'id' => [
                    'title' => 'Migrasi Cloud & Monitoring',
                    'slug' => 'migrasi-cloud-monitoring',
                    'excerpt' => 'Pindah VPS ke cluster dengan Grafana dashboard.',
                    'content' => '<p>Zero-downtime migration dan playbook incident response.</p>',
                ],
                'en' => [
                    'title' => 'Cloud Migration & Monitoring',
                    'slug' => 'cloud-migration-monitoring',
                    'excerpt' => 'VPS migration to clustered setup with Grafana dashboards.',
                    'content' => '<p>Zero-downtime migration and incident response playbooks.</p>',
                ],
            ],
            [
                'sort_order' => 4,
                'category_id' => $webCategory->id,
                'client_name' => 'Startup EduTech',
                'id' => [
                    'title' => 'Landing Campaign SaaS',
                    'slug' => 'landing-campaign-saas',
                    'excerpt' => 'Landing page A/B test untuk kampanye ads.',
                    'content' => '<p>Integrasi analytics dan form lead ke CRM klien.</p>',
                ],
                'en' => [
                    'title' => 'SaaS Campaign Landing',
                    'slug' => 'saas-campaign-landing',
                    'excerpt' => 'A/B tested landing page for ad campaigns.',
                    'content' => '<p>Analytics integration and lead forms connected to client CRM.</p>',
                ],
            ],
        ];

        foreach ($projects as $project) {
            $portfolio = Portfolio::query()->create([
                'cover_id' => null,
                'client_name' => $project['client_name'],
                'project_date' => now()->subMonths($project['sort_order'] * 2)->toDateString(),
                'project_url' => null,
                'category_id' => $project['category_id'],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => $project['sort_order'],
            ]);

            $portfolio->translateOrNew('id')->fill($project['id'])->save();
            $portfolio->translateOrNew('en')->fill($project['en'])->save();
        }
    }

    protected function seedTeamMembers(): void
    {
        if (Team::query()->whereHas('translations', fn ($q) => $q->where('locale', 'id')->where('slug', 'budi-santoso'))->exists()) {
            return;
        }

        $members = [
            [
                'sort_order' => 1,
                'email' => 'budi@servicepro.demo',
                'id' => ['name' => 'Budi Santoso', 'slug' => 'budi-santoso', 'position' => 'Lead Developer', 'bio' => 'Spesialis Laravel dan arsitektur modular.'],
                'en' => ['name' => 'Budi Santoso', 'slug' => 'budi-santoso', 'position' => 'Lead Developer', 'bio' => 'Laravel and modular architecture specialist.'],
            ],
            [
                'sort_order' => 2,
                'email' => 'sari@servicepro.demo',
                'id' => ['name' => 'Sari Wijaya', 'slug' => 'sari-wijaya', 'position' => 'UI/UX Designer', 'bio' => 'Fokus pada desain conversion-oriented.'],
                'en' => ['name' => 'Sari Wijaya', 'slug' => 'sari-wijaya', 'position' => 'UI/UX Designer', 'bio' => 'Focused on conversion-oriented design.'],
            ],
            [
                'sort_order' => 3,
                'email' => 'andi@servicepro.demo',
                'id' => ['name' => 'Andi Pratama', 'slug' => 'andi-pratama', 'position' => 'DevOps Engineer', 'bio' => 'CI/CD, Docker, dan observability.'],
                'en' => ['name' => 'Andi Pratama', 'slug' => 'andi-pratama', 'position' => 'DevOps Engineer', 'bio' => 'CI/CD, Docker, and observability.'],
            ],
            [
                'sort_order' => 4,
                'email' => 'rina@servicepro.demo',
                'id' => ['name' => 'Rina Kusuma', 'slug' => 'rina-kusuma', 'position' => 'Account Manager', 'bio' => 'Menjaga komunikasi klien dan timeline proyek.'],
                'en' => ['name' => 'Rina Kusuma', 'slug' => 'rina-kusuma', 'position' => 'Account Manager', 'bio' => 'Keeps client communication and project timelines on track.'],
            ],
        ];

        foreach ($members as $member) {
            $team = Team::query()->create([
                'photo_id' => null,
                'email' => $member['email'],
                'social_links' => null,
                'is_active' => true,
                'sort_order' => $member['sort_order'],
            ]);

            $team->translateOrNew('id')->fill($member['id'])->save();
            $team->translateOrNew('en')->fill($member['en'])->save();
        }
    }

    protected function seedTestimonials(): void
    {
        if (Testimonial::query()->whereHas('translations', fn ($q) => $q->where('locale', 'id')->where('author_name', 'Siti Rahma'))->exists()) {
            return;
        }

        $items = [
            [
                'rating' => 5,
                'sort_order' => 1,
                'id' => [
                    'author_name' => 'Siti Rahma',
                    'author_position' => 'Direktur Operasional',
                    'author_company' => 'PT Nusantara Retail',
                    'content' => 'Tim responsif dan website baru kami stabil saat traffic puncak.',
                ],
                'en' => [
                    'author_name' => 'Siti Rahma',
                    'author_position' => 'COO',
                    'author_company' => 'Nusantara Retail',
                    'content' => 'Responsive team and our new site stayed stable at peak traffic.',
                ],
            ],
            [
                'rating' => 5,
                'sort_order' => 2,
                'id' => [
                    'author_name' => 'Michael Tan',
                    'author_position' => 'Founder',
                    'author_company' => 'Startup EduTech',
                    'content' => 'Landing page kampanye kami conversion rate-nya naik 40%.',
                ],
                'en' => [
                    'author_name' => 'Michael Tan',
                    'author_position' => 'Founder',
                    'author_company' => 'EduTech Startup',
                    'content' => 'Our campaign landing page improved conversion by 40%.',
                ],
            ],
            [
                'rating' => 4,
                'sort_order' => 3,
                'id' => [
                    'author_name' => 'Dr. Hendra',
                    'author_position' => 'Owner',
                    'author_company' => 'Klinik Sehat Bersama',
                    'content' => 'Proses booking online sangat membantu resepsionis kami.',
                ],
                'en' => [
                    'author_name' => 'Dr. Hendra',
                    'author_position' => 'Owner',
                    'author_company' => 'Sehat Bersama Clinic',
                    'content' => 'Online booking made life easier for our front desk.',
                ],
            ],
            [
                'rating' => 5,
                'sort_order' => 4,
                'id' => [
                    'author_name' => 'Dewi Lestari',
                    'author_position' => 'IT Manager',
                    'author_company' => 'Logistik Express',
                    'content' => 'Migrasi server tanpa downtime — dokumentasinya lengkap.',
                ],
                'en' => [
                    'author_name' => 'Dewi Lestari',
                    'author_position' => 'IT Manager',
                    'author_company' => 'Logistik Express',
                    'content' => 'Zero-downtime server migration with thorough documentation.',
                ],
            ],
            [
                'rating' => 5,
                'sort_order' => 5,
                'id' => [
                    'author_name' => 'Agus Firmansyah',
                    'author_position' => 'CMO',
                    'author_company' => 'Brand Lokal',
                    'content' => 'Maintenance bulanan worth it — bug kecil selalu ditangani cepat.',
                ],
                'en' => [
                    'author_name' => 'Agus Firmansyah',
                    'author_position' => 'CMO',
                    'author_company' => 'Local Brand',
                    'content' => 'Monthly maintenance is worth it — small bugs fixed quickly.',
                ],
            ],
        ];

        foreach ($items as $item) {
            $testimonial = Testimonial::query()->create([
                'photo_id' => null,
                'rating' => $item['rating'],
                'is_active' => true,
                'sort_order' => $item['sort_order'],
            ]);

            $testimonial->translateOrNew('id')->fill($item['id'])->save();
            $testimonial->translateOrNew('en')->fill($item['en'])->save();
        }
    }
}

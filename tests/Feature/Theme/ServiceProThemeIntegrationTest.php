<?php

namespace Tests\Feature\Theme;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SeedsServiceProTheme;
use Tests\TestCase;

class ServiceProThemeIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsServiceProTheme;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedServiceProEnvironment();
    }

    public function test_homepage_renders_all_sections(): void
    {
        $response = $this->get('/id');

        $response->assertOk();

        $response->assertSee('Solusi Profesional untuk Bisnis Anda', false);
        $response->assertSee('Paket Harga', false);
        $response->assertSee('Pertanyaan Umum', false);
        $response->assertSee('Artikel Terbaru', false);
        $response->assertSee('Migrasi VPS ke Cloud tanpa Downtime', false);
        $response->assertSee('Portal E-commerce Nusantara', false);
        $response->assertSee('Budi Santoso', false);
        $response->assertSee('Siti Rahma', false);
    }

    public function test_all_theme_routes_accessible(): void
    {
        $paths = [
            '/id',
            '/id/about',
            '/id/services',
            '/id/portfolio',
            '/id/team',
            '/id/testimonials',
            '/id/pricing',
            '/id/faq',
            '/id/contact',
            '/id/blog',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/id/portfolio/portal-ecommerce-nusantara')->assertOk();
        $this->get('/id/blog/membangun-cms-modular-laravel')->assertOk();
    }

    public function test_multilanguage_all_pages(): void
    {
        $this->get('/id/about')
            ->assertOk()
            ->assertSee('Menjadi partner digital terpercaya di Indonesia.', false);

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('To become a trusted digital partner in Indonesia.', false);

        $this->get('/id/pricing')->assertSee('Paket Harga', false);
        $this->get('/en/pricing')->assertSee('Pricing Plans', false);

        $this->get('/id/faq')->assertSee('Berapa lama pengerjaan website?', false);
        $this->get('/en/faq')->assertSee('How long does a website take?', false);
    }

    public function test_no_hardcoded_content_in_views(): void
    {
        $themePath = resource_path('views/themes/servicepro');
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($themePath, \FilesystemIterator::SKIP_DOTS)
        );

        $violations = [];

        foreach ($files as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            if ($contents === false) {
                continue;
            }

            $stripped = $contents;
            $stripped = preg_replace('/@php[\s\S]*?@endphp/', '', $stripped) ?? $stripped;
            $stripped = preg_replace('/<script[\s\S]*?<\/script>/i', '', $stripped) ?? $stripped;
            $stripped = preg_replace('/\{\{[\s\S]*?\}\}/', '', $stripped) ?? $stripped;
            $stripped = preg_replace('/\{!![\s\S]*?!!\}/', '', $stripped) ?? $stripped;
            $stripped = preg_replace('/@[a-zA-Z][^\n]*/', '', $stripped) ?? $stripped;

            if (preg_match_all('/>([^<]+)</u', $stripped, $matches)) {
                foreach ($matches[1] as $text) {
                    $trimmed = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);

                    if (mb_strlen($trimmed) < 45) {
                        continue;
                    }

                    if (preg_match('/[\$@{}:<>\/\\\\=]|=>|fn\s*\(|::|\?\?/', $trimmed)) {
                        continue;
                    }

                    if (! preg_match('/[\p{L}]{8,}/u', $trimmed)) {
                        continue;
                    }

                    $violations[] = $file->getPathname().': '.$trimmed;
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Teks statis panjang ditemukan di Blade theme:\n".implode("\n", array_slice($violations, 0, 10))
        );
    }
}

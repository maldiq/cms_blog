<?php

namespace Tests\Feature;

use App\Domain\Service\Models\Service;
use App\Domain\Service\Services\SolusiwebServiceImportService;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SolusiwebServiceImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
    }

    public function test_import_uses_rendered_service_template_html(): void
    {
        Service::factory()->create(['is_active' => true]);

        $renderedHtml = <<<'HTML'
<html><head>
<style id="sw-landing-inline-css">.sw-landing{color:#334155;}</style>
<style id="sw-service-inline-css">.sw-service-hero{padding:1rem;}</style>
</head><body>
<article class="sw-landing sw-service">
<div class="entry-content">
<section class="sw-section sw-service-hero"><h1 id="sw-service-hero-title">Jasa Perbaikan Website</h1><p class="sw-hero__lead">Lead hero.</p></section>
<p>Rp500.000</p>
</div>
</article>
</body></html>
HTML;

        Http::fake(function ($request) use ($renderedHtml) {
            $url = $request->url();

            if (str_contains($url, '/wp-json/wp/v2/pages')) {
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
                $slug = $query['slug'] ?? null;

                if ($slug !== null && in_array($slug, SolusiwebServiceImportService::SERVICE_SLUGS, true)) {
                    return Http::response([
                        [
                            'slug' => $slug,
                            'title' => ['rendered' => 'Judul '.$slug],
                            'content' => ['rendered' => '<p>fallback api</p>'],
                            'excerpt' => ['rendered' => ''],
                            'yoast_head_json' => ['title' => 'SEO', 'description' => 'Desc'],
                        ],
                    ]);
                }

                return Http::response([]);
            }

            if (str_contains($url, '/layanan/')) {
                return Http::response($renderedHtml, 200, ['Content-Type' => 'text/html']);
            }

            return Http::response([], 404);
        });

        $result = app(SolusiwebServiceImportService::class)->import('http://solusiweb.test');

        $this->assertSame(5, $result['imported']);
        $this->assertSame(5, Service::query()->count());

        $service = Service::query()->whereHas('translations', fn ($q) => $q->where('slug', 'jasa-perbaikan-website'))->first();
        $this->assertNotNull($service);
        $this->assertStringContainsString('sw-service-hero', (string) $service->translate('id', false)?->content);
        $this->assertSame(500000.0, (float) $service->price_from);
    }
}

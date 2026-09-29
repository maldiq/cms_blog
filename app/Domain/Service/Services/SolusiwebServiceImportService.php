<?php

namespace App\Domain\Service\Services;

use App\Domain\Menu\Services\HeaderMenuFromContentService;
use App\Domain\Service\Models\Service;
use App\Domain\Service\Models\ServiceTranslation;
use App\Domain\Service\Support\SolusiwebRenderedHtmlExtractor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SolusiwebServiceImportService
{
    /**
     * Layanan yang diimpor (sesuai menu solusiweb.test).
     *
     * @var list<string>
     */
    public const SERVICE_SLUGS = [
        'mengapa-backup-website-itu-penting',
        'instalasi-jasa-setting-vps',
        'jasa-maintenance-wordpress',
        'jasa-pembuatan-website-profesional-seo-friendly',
        'jasa-perbaikan-website',
    ];

    public function __construct(
        private readonly HeaderMenuFromContentService $headerMenuFromContentService,
        private readonly SolusiwebRenderedHtmlExtractor $renderedHtmlExtractor,
    ) {}

    /**
     * @return array{imported: int, source: string}
     */
    public function import(?string $baseUrl = null): array
    {
        $baseUrl = rtrim($baseUrl ?? (string) config('import.solusiweb_url'), '/');

        $this->renderedHtmlExtractor->syncStylesheet($baseUrl);

        $imported = 0;

        Service::withoutEvents(function () use ($baseUrl, &$imported): void {
            ServiceTranslation::query()->delete();
            Service::query()->delete();

            $sortOrder = 1;

            foreach (self::SERVICE_SLUGS as $slug) {
                if ($this->importServiceBySlug($baseUrl, $slug, $sortOrder)) {
                    $imported++;
                    $sortOrder++;
                }
            }
        });

        $this->headerMenuFromContentService->sync();

        return [
            'imported' => $imported,
            'source' => $baseUrl,
        ];
    }

    protected function importServiceBySlug(string $baseUrl, string $slug, int $sortOrder): bool
    {
        $pageUrl = "{$baseUrl}/layanan/{$slug}/";
        $apiPage = $this->fetchApiPage($baseUrl, $slug);

        if ($apiPage === null) {
            return false;
        }

        $rendered = $this->renderedHtmlExtractor->extractFromUrl($pageUrl);

        $content = $rendered['content'] !== ''
            ? $rendered['content']
            : trim((string) ($apiPage['content']['rendered'] ?? ''));

        if ($content === '') {
            return false;
        }

        $title = $rendered['title']
            ?? html_entity_decode(strip_tags((string) ($apiPage['title']['rendered'] ?? 'Layanan')), ENT_QUOTES, 'UTF-8');

        $excerpt = $rendered['excerpt'] ?? $this->resolveExcerpt($apiPage, $content);
        $meta = is_array($apiPage['yoast_head_json'] ?? null) ? $apiPage['yoast_head_json'] : [];

        $service = Service::query()->create([
            'icon' => $this->iconForSlug($slug),
            'cover_id' => null,
            'price_from' => $this->extractPriceFromHtml($content),
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);

        $translationPayload = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'meta_title' => (string) ($meta['title'] ?? $title),
            'meta_description' => (string) ($meta['description'] ?? $excerpt),
            'meta_keywords' => null,
        ];

        $service->translateOrNew('id')->fill($translationPayload)->save();
        $service->translateOrNew('en')->fill($translationPayload)->save();

        return true;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchApiPage(string $baseUrl, string $slug): ?array
    {
        $response = Http::timeout(60)
            ->acceptJson()
            ->get("{$baseUrl}/wp-json/wp/v2/pages", [
                'slug' => $slug,
                '_fields' => 'id,slug,link,title,content,excerpt,yoast_head_json',
            ]);

        $response->throw();

        $page = $response->json()[0] ?? null;

        return is_array($page) ? $page : null;
    }

    /**
     * @param  array<string, mixed>  $page
     */
    protected function resolveExcerpt(array $page, string $content): string
    {
        $excerptHtml = (string) ($page['excerpt']['rendered'] ?? '');
        $excerpt = trim(html_entity_decode(strip_tags($excerptHtml), ENT_QUOTES, 'UTF-8'));
        $excerpt = preg_replace('/\s*Read more.*$/iu', '', $excerpt) ?? $excerpt;

        if ($excerpt !== '') {
            return Str::limit($excerpt, 500);
        }

        $meta = is_array($page['yoast_head_json'] ?? null) ? $page['yoast_head_json'] : [];
        $yoast = trim((string) ($meta['description'] ?? ''));

        if ($yoast !== '') {
            return Str::limit($yoast, 500);
        }

        return Str::limit(trim(strip_tags($content)), 500);
    }

    protected function extractPriceFromHtml(string $html): ?float
    {
        if (preg_match('/Rp\s*([\d.,]+)/i', strip_tags($html), $matches) !== 1) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $matches[1]) ?? '';

        if ($digits === '') {
            return null;
        }

        $value = (float) $digits;

        return $value > 0 ? $value : null;
    }

    protected function iconForSlug(string $slug): string
    {
        return match (true) {
            str_contains($slug, 'backup') => '💾',
            str_contains($slug, 'vps') || str_contains($slug, 'instalasi') => '🖥️',
            str_contains($slug, 'maintenance') || str_contains($slug, 'wordpress') => '🔧',
            str_contains($slug, 'pembuatan') => '🌐',
            str_contains($slug, 'perbaikan') => '🛠️',
            default => '⚙️',
        };
    }
}

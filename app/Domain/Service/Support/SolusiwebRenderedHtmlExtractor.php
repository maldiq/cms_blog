<?php

namespace App\Domain\Service\Support;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SolusiwebRenderedHtmlExtractor
{
    /**
     * @return array{content: string, title: ?string, excerpt: ?string}
     */
    public function extractFromUrl(string $pageUrl, string $locale = 'id'): array
    {
        $response = Http::timeout(60)->get($pageUrl);
        $response->throw();

        $html = $response->body();

        return $this->extractFromHtml($html, $locale);
    }

    /**
     * @return array{content: string, title: ?string, excerpt: ?string}
     */
    public function extractFromHtml(string $html, string $locale = 'id'): array
    {
        $content = $this->extractEntryContentHtml($html);

        if ($content === '') {
            return [
                'content' => '',
                'title' => null,
                'excerpt' => null,
            ];
        }

        $content = $this->rewriteLinks($content, $locale);

        return [
            'content' => $content,
            'title' => $this->extractHeroTitle($html),
            'excerpt' => $this->extractHeroLead($html),
        ];
    }

    /** Panjang minimal CSS hasil scrape; di bawah ini file publik tidak ditimpa (hindari mock test / halaman kosong). */
    private const MIN_STYLESHEET_BYTES = 4096;

    public function syncStylesheet(string $baseUrl): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $sampleUrl = rtrim($baseUrl, '/').'/layanan/mengapa-backup-website-itu-penting/';
        $response = Http::timeout(60)->get($sampleUrl);
        $response->throw();

        $html = $response->body();
        $css = $this->extractInlineStyles($html);

        if ($css === '') {
            return;
        }

        $target = public_path('themes/servicepro/solusiweb-service.css');

        if (strlen($css) < self::MIN_STYLESHEET_BYTES && is_file($target) && filesize($target) >= self::MIN_STYLESHEET_BYTES) {
            return;
        }

        $directory = dirname($target);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($target, $css);
    }

    protected function extractEntryContentHtml(string $html): string
    {
        $previous = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpath = new DOMXPath($dom);
        $entry = $xpath->query("//article[contains(concat(' ', normalize-space(@class), ' '), ' sw-service ')]//div[contains(concat(' ', normalize-space(@class), ' '), ' entry-content ')]")->item(0);

        if ($entry === null) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            return '';
        }

        $inner = '';

        foreach ($entry->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return trim($inner);
    }

    protected function extractHeroTitle(string $html): ?string
    {
        if (preg_match('/<h1[^>]*id="sw-service-hero-title"[^>]*>(.*?)<\/h1>/is', $html, $matches) === 1) {
            $title = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES, 'UTF-8'));

            return $title !== '' ? $title : null;
        }

        if (preg_match('/<meta\s+itemprop="name"\s+content="([^"]+)"/i', $html, $matches) === 1) {
            return trim(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'));
        }

        return null;
    }

    protected function extractHeroLead(string $html): ?string
    {
        if (preg_match('/<p\s+class="[^"]*sw-hero__lead[^"]*"[^>]*>(.*?)<\/p>/is', $html, $matches) === 1) {
            $lead = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES, 'UTF-8'));

            return $lead !== '' ? Str::limit($lead, 500) : null;
        }

        return null;
    }

    protected function extractInlineStyles(string $html): string
    {
        $blocks = [];

        foreach (['sw-landing-inline-css', 'sw-service-inline-css'] as $id) {
            if (preg_match('/<style\s+id="'.preg_quote($id, '/').'"[^>]*>(.*?)<\/style>/is', $html, $matches) === 1) {
                $blocks[] = trim($matches[1]);
            }
        }

        return implode("\n\n", $blocks);
    }

    protected function rewriteLinks(string $html, string $locale): string
    {
        $home = route('home', ['locale' => $locale]);
        $servicesIndex = route('services.index', ['locale' => $locale]);
        $contact = route('contact', ['locale' => $locale]);

        $html = preg_replace_callback(
            '/href=(["\'])(https?:\/\/(?:www\.)?solusiweb\.(?:net|test)\/layanan\/([^\/\'"]+)\/?)\1/i',
            function (array $matches) use ($locale): string {
                $slug = rtrim($matches[2], '/');
                $url = route('services.show', ['locale' => $locale, 'slug' => $slug]);

                return 'href='.$matches[1].$url.$matches[1];
            },
            $html,
        ) ?? $html;

        $patterns = [
            '/href=(["\'])(https?:\/\/(?:www\.)?solusiweb\.(?:net|test)\/?)\1/i' => 'href=$1'.$home.'$1',
            '/href=(["\'])(https?:\/\/(?:www\.)?solusiweb\.(?:net|test)\/layanan\/?)\1/i' => 'href=$1'.$servicesIndex.'$1',
            '/href=(["\'])(https?:\/\/(?:www\.)?solusiweb\.(?:net|test)\/kontak-kami\/?)\1/i' => 'href=$1'.$contact.'$1',
            '/href=(["\'])\/kontak-kami\/?\1/i' => 'href=$1'.$contact.'$1',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html) ?? $html;
        }

        // Gambar: prefer host lokal jika wp-content ada di solusiweb.test
        $html = str_replace('https://solusiweb.net/wp-content/', config('import.solusiweb_url').'/wp-content/', $html);
        $html = str_replace('http://solusiweb.net/wp-content/', config('import.solusiweb_url').'/wp-content/', $html);

        return $html;
    }
}

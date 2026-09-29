<?php

namespace Database\Seeders;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Post\Models\PostTranslation;
use App\Domain\Menu\Services\HeaderMenuFromContentService;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class ServiceProBlogSeeder extends Seeder
{
    public function run(): void
    {
        if (PostTranslation::query()->where('locale', 'id')->where('slug', 'membangun-cms-modular-laravel')->exists()) {
            return;
        }

        $author = User::query()->where('email', 'admin@admin.com')->first();

        if ($author === null) {
            return;
        }

        $category = Category::query()->create(['is_active' => true, 'sort_order' => 1]);
        $category->translateOrNew('id')->fill([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
            'description' => 'Artikel seputar pengembangan web dan infrastruktur.',
        ])->save();
        $category->translateOrNew('en')->fill([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Articles about web development and infrastructure.',
        ])->save();

        $articles = [
            [
                'id' => [
                    'title' => 'Membangun CMS Modular dengan Laravel 12',
                    'slug' => 'membangun-cms-modular-laravel',
                    'excerpt' => 'Struktur domain, Filament, dan Livewire untuk tim kecil yang butuh skala.',
                    'content' => '<p>Kami memecah fitur ke folder <code>app/Domain</code> agar blog, layanan, dan theme tidak saling coupling.</p>',
                ],
                'en' => [
                    'title' => 'Building a Modular CMS with Laravel 12',
                    'slug' => 'building-modular-cms-laravel',
                    'excerpt' => 'Domain folders, Filament, and Livewire for small teams that need to scale.',
                    'content' => '<p>We split features into <code>app/Domain</code> so blog, services, and theme stay decoupled.</p>',
                ],
            ],
            [
                'id' => [
                    'title' => 'Checklist Keamanan Website Perusahaan',
                    'slug' => 'checklist-keamanan-website',
                    'excerpt' => 'HTTPS, backup, rate limit, dan hardening admin panel.',
                    'content' => '<p>Prioritas pertama: patch rutin, WAF dasar, dan audit permission Filament.</p>',
                ],
                'en' => [
                    'title' => 'Corporate Website Security Checklist',
                    'slug' => 'corporate-website-security-checklist',
                    'excerpt' => 'HTTPS, backups, rate limits, and admin panel hardening.',
                    'content' => '<p>Start with regular patches, a basic WAF, and Filament permission audits.</p>',
                ],
            ],
            [
                'id' => [
                    'title' => 'Optimasi Core Web Vitals untuk Landing Page',
                    'slug' => 'optimasi-core-web-vitals',
                    'excerpt' => 'Lazy load, font display, dan cache section theme.',
                    'content' => '<p>Gambar responsif dan cache fragment homepage sering menaikkan skor Lighthouse secara signifikan.</p>',
                ],
                'en' => [
                    'title' => 'Core Web Vitals for Landing Pages',
                    'slug' => 'core-web-vitals-landing-pages',
                    'excerpt' => 'Lazy loading, font display, and theme section caching.',
                    'content' => '<p>Responsive images and homepage fragment caching often boost Lighthouse scores.</p>',
                ],
            ],
            [
                'id' => [
                    'title' => 'Multibahasa id/en dengan Spatie Translatable',
                    'slug' => 'multibahasa-spatie-translatable',
                    'excerpt' => 'Slug per locale dan routing berprefix locale.',
                    'content' => '<p>Setiap konten disimpan per locale; URL <code>/id</code> dan <code>/en</code> konsisten untuk SEO.</p>',
                ],
                'en' => [
                    'title' => 'id/en Multilingual with Spatie Translatable',
                    'slug' => 'multilingual-spatie-translatable',
                    'excerpt' => 'Per-locale slugs and locale-prefixed routing.',
                    'content' => '<p>Content is stored per locale; <code>/id</code> and <code>/en</code> URLs stay SEO-friendly.</p>',
                ],
            ],
            [
                'id' => [
                    'title' => 'Filament v3 untuk Panel Kelola CMS',
                    'slug' => 'filament-panel-kelola-cms',
                    'excerpt' => 'Resource domain, theme editor, dan permission granular.',
                    'content' => '<p>Panel di <code>/kelola</code> menggabungkan blog, layanan, media, dan pengaturan theme.</p>',
                ],
                'en' => [
                    'title' => 'Filament v3 for the CMS Admin Panel',
                    'slug' => 'filament-cms-admin-panel',
                    'excerpt' => 'Domain resources, theme editor, and granular permissions.',
                    'content' => '<p>The <code>/kelola</code> panel combines blog, services, media, and theme settings.</p>',
                ],
            ],
            [
                'id' => [
                    'title' => 'Migrasi VPS ke Cloud tanpa Downtime',
                    'slug' => 'migrasi-vps-cloud-tanpa-downtime',
                    'excerpt' => 'Blue-green deploy dan playbook rollback.',
                    'content' => '<p>Replikasi database, DNS TTL rendah, dan health check sebelum cutover.</p>',
                ],
                'en' => [
                    'title' => 'Zero-Downtime VPS to Cloud Migration',
                    'slug' => 'zero-downtime-vps-cloud-migration',
                    'excerpt' => 'Blue-green deploys and rollback playbooks.',
                    'content' => '<p>Database replication, low DNS TTL, and health checks before cutover.</p>',
                ],
            ],
        ];

        foreach ($articles as $index => $article) {
            $post = Post::query()->create([
                'user_id' => $author->id,
                'series_id' => null,
                'series_order' => null,
                'status' => Post::STATUS_PUBLISHED,
                'type' => 'article',
                'published_at' => now()->subDays(6 - $index),
                'is_featured' => $index === 0,
                'allow_comment' => true,
            ]);

            $post->translateOrNew('id')->fill($article['id'])->save();
            $post->translateOrNew('en')->fill($article['en'])->save();

            $post->load('translations');
            $post->recalculateReadingTime();
            $post->saveQuietly();

            $post->categories()->sync([$category->id]);
        }

        app(HeaderMenuFromContentService::class)->sync();
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Menu\Services\HeaderMenuFromContentService;
use App\Domain\Menu\Services\MenuService;
use App\Domain\Service\Models\Service;
use App\Domain\Service\Support\ServiceDemoCatalog;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HeaderMenuFromContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
        Cache::flush();
        app()->setLocale('id');
    }

    public function test_sync_builds_layanan_children_from_services_with_content(): void
    {
        ServiceDemoCatalog::seedIfMissing();

        app(HeaderMenuFromContentService::class)->sync();

        $tree = app(MenuService::class)->buildTree('header');
        $layanan = $tree->firstWhere('label', 'Layanan');

        $this->assertNotNull($layanan);
        $this->assertGreaterThanOrEqual(5, count($layanan['children']));
        $this->assertTrue(
            collect($layanan['children'])->contains(fn (array $child): bool => str_contains($child['label'], 'Backup Website Data')),
        );

        $this->assertDatabaseHas('menu_items', [
            'type' => MenuItem::TYPE_SERVICE,
        ]);
    }

    public function test_blog_link_appears_when_published_posts_exist(): void
    {
        ServiceDemoCatalog::seedIfMissing();

        $author = User::factory()->create(['is_active' => true]);

        Post::query()->create([
            'user_id' => $author->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now()->subDay(),
        ])->translateOrNew('id')->fill([
            'title' => 'Artikel Test',
            'slug' => 'artikel-test',
            'content' => '<p>Isi artikel.</p>',
        ])->save();

        app(HeaderMenuFromContentService::class)->sync();

        $tree = app(MenuService::class)->buildTree('header');

        $this->assertNotNull($tree->firstWhere('label', 'Blog'));
    }

    public function test_service_menu_item_resolves_to_service_show_route(): void
    {
        $service = Service::factory()->create(['is_active' => true, 'sort_order' => 1]);
        $service->translateOrNew('id')->fill([
            'title' => 'Layanan Test',
            'slug' => 'layanan-test',
            'excerpt' => 'Ringkasan.',
            'content' => '<p>Konten layanan.</p>',
        ])->save();

        $item = MenuItem::factory()->create([
            'type' => MenuItem::TYPE_SERVICE,
            'target_id' => $service->id,
            'url' => null,
        ]);

        app()->setLocale('id');

        $this->assertSame(
            route('services.show', ['locale' => 'id', 'slug' => 'layanan-test']),
            $item->resolveUrl(),
        );
    }
}

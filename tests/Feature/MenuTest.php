<?php

namespace Tests\Feature;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Menu\Services\MenuService;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        app()->setLocale('id');
    }

    public function test_can_create_menu_with_items(): void
    {
        $menu = Menu::factory()->create([
            'name' => 'Header Utama',
            'location' => 'header',
            'is_active' => true,
        ]);

        $parent = MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'type' => MenuItem::TYPE_LINK,
            'url' => '/id/about',
            'label' => ['id' => 'Tentang', 'en' => 'About'],
            'sort_order' => 1,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'type' => MenuItem::TYPE_LINK,
            'url' => '/id/team',
            'label' => ['id' => 'Tim', 'en' => 'Team'],
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'location' => 'header']);
        $this->assertDatabaseCount('menu_items', 2);

        $tree = app(MenuService::class)->buildTree('header');
        $this->assertCount(1, $tree);
        $this->assertCount(1, $tree->first()['children']);
    }

    public function test_menu_tree_cached(): void
    {
        Cache::flush();

        $menu = Menu::factory()->create([
            'location' => 'header',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => ['id' => 'Beranda', 'en' => 'Home'],
            'url' => '/',
        ]);

        $service = app(MenuService::class);
        $key = MenuService::cacheKey('header', 'id');

        $this->assertFalse(Cache::has($key));

        $service->buildTree('header');

        $this->assertTrue(Cache::has($key));

        MenuItem::query()->where('menu_id', $menu->id)->delete();
        $menu->update(['name' => 'Updated']);

        $this->assertFalse(Cache::has($key));
    }

    public function test_role_visibility_filters_items(): void
    {
        $menu = Menu::factory()->create([
            'location' => 'header',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => ['id' => 'Publik', 'en' => 'Public'],
            'url' => '/public',
            'roles' => null,
            'sort_order' => 1,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => ['id' => 'Admin Only', 'en' => 'Admin Only'],
            'url' => '/admin-area',
            'roles' => ['admin'],
            'sort_order' => 2,
        ]);

        $service = app(MenuService::class);
        Cache::flush();
        $tree = $service->buildTree('header');

        $guestTree = $service->filterByRole($tree, null);
        $this->assertCount(1, $guestTree);
        $this->assertSame('Publik', $guestTree->first()['label']);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $adminTree = $service->filterByRole($tree, $admin);
        $this->assertCount(2, $adminTree);
    }
}

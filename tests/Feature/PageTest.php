<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Page\Models\Page;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
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

    public function test_can_create_page_with_translations(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $page = Page::query()->create([
            'user_id' => $user->id,
            'template' => 'default',
            'status' => Page::STATUS_DRAFT,
        ]);

        $page->translateOrNew('id')->fill([
            'title' => 'Tentang',
            'slug' => 'tentang',
            'content' => '<p>Halo</p>',
        ])->save();

        $page->translateOrNew('en')->fill([
            'title' => 'About',
            'slug' => 'about',
            'content' => '<p>Hello</p>',
        ])->save();

        $this->assertDatabaseHas('page_translations', ['page_id' => $page->id, 'locale' => 'id', 'slug' => 'tentang']);
        $this->assertDatabaseHas('page_translations', ['page_id' => $page->id, 'locale' => 'en', 'slug' => 'about']);
    }

    public function test_homepage_displays_affiliate_products_instead_of_cms_homepage(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $page = Page::query()->create([
            'user_id' => $user->id,
            'template' => 'default',
            'is_homepage' => true,
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $page->translateOrNew('id')->fill([
            'title' => 'Beranda Kustom',
            'slug' => 'beranda',
            'content' => '<p>Ini homepage kustom.</p>',
        ])->save();

        AffiliateProduct::query()->create([
            'title' => 'Produk Beranda',
            'slug' => 'produk-beranda',
            'category' => 'Elektronik',
            'platform' => 'shopee',
            'description' => 'Produk pilihan untuk halaman depan.',
            'affiliate_url' => 'https://shopee.co.id/product/123',
            'is_active' => true,
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Temukan Produk Pilihan')
            ->assertSee('Produk Beranda')
            ->assertDontSee('Beranda Kustom')
            ->assertSee('Kategori Affiliate')
            ->assertSee('Elektronik')
            ->assertSee('category=Elektronik');
    }

    public function test_homepage_shows_empty_catalog_and_affiliate_category_menu_without_active_products(): void
    {
        $this->get('/id')
            ->assertOk()
            ->assertSee('Temukan Produk Pilihan')
            ->assertSee('Belum ada produk yang cocok')
            ->assertSee('Kategori Affiliate')
            ->assertSee('Kategori akan muncul setelah produk diaktifkan.');
    }

    public function test_published_page_visible(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $page = Page::query()->create([
            'user_id' => $user->id,
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $page->translateOrNew('id')->fill([
            'title' => 'Tentang Kami',
            'slug' => 'about',
            'content' => '<p>Konten tentang.</p>',
        ])->save();

        $page->translateOrNew('en')->fill([
            'title' => 'About Us',
            'slug' => 'about',
            'content' => '<p>About content.</p>',
        ])->save();

        $this->get('/id/page/about')->assertOk()->assertSee('Tentang Kami');
        $this->get('/en/page/about')->assertOk()->assertSee('About Us');

        $page->update(['status' => Page::STATUS_DRAFT]);
        $this->get('/id/page/about')->assertNotFound();
    }
}

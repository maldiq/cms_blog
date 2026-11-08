<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\CreateAffiliateProduct;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\EditAffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\Seo\Http\Controllers\SitemapController;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateProductSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }

    public function test_active_products_are_listed_and_can_be_filtered(): void
    {
        $this->makeProduct('Headphone Wireless', 'headphone-wireless', 'shopee', 'Elektronik');
        $this->makeProduct('Tas Harian', 'tas-harian', 'tiktok_shop', 'Fashion');
        $this->makeProduct('Buku Teknologi', 'buku-teknologi', 'amazon', 'Buku');
        $this->makeProduct('Hosting VPS', 'hosting-vps', 'vps', 'Hosting & VPS');
        $this->makeProduct('Produk tersembunyi', 'produk-tersembunyi', 'shopee', 'Elektronik', false);

        $this->get('/id/products')
            ->assertOk()
            ->assertSee('Headphone Wireless')
            ->assertSee('Tas Harian')
            ->assertDontSee('Produk tersembunyi');

        $this->get('/id/products?platform=shopee&category=Elektronik')
            ->assertOk()
            ->assertSee('Headphone Wireless')
            ->assertDontSee('Tas Harian')
            ->assertDontSee('Buku Teknologi');

        $this->get('/id/products?platform=vps')
            ->assertOk()
            ->assertSee('Hosting VPS')
            ->assertDontSee('Buku Teknologi');

        $this->get('/id/products?platform[]=vps')->assertOk();
    }

    public function test_active_product_detail_is_visible_and_inactive_detail_is_not_found(): void
    {
        $this->makeProduct('Rekomendasi Aktif', 'rekomendasi-aktif');
        $this->makeProduct('Rekomendasi Nonaktif', 'rekomendasi-nonaktif', 'shopee', 'Umum', false);

        $this->get('/id/products/rekomendasi-aktif')
            ->assertOk()
            ->assertSee('Rekomendasi Aktif')
            ->assertSee('Tautan ini adalah tautan affiliate.');

        $this->get('/id/products/rekomendasi-nonaktif')->assertNotFound();
    }

    public function test_affiliate_redirect_tracks_click_and_uses_stored_external_url(): void
    {
        $product = $this->makeProduct('Kabel USB', 'kabel-usb');

        $this->get('/id/go/kabel-usb')
            ->assertRedirect('https://shopee.co.id/product/123');

        $this->assertSame(1, $product->fresh()->click_count);
    }

    public function test_invalid_affiliate_url_does_not_redirect_or_count_click(): void
    {
        $product = $this->makeProduct('Produk dengan URL salah', 'url-salah');
        $product->update(['affiliate_url' => 'javascript:alert(1)']);

        $this->get('/id/go/url-salah')->assertNotFound();

        $this->assertSame(0, $product->fresh()->click_count);
    }

    public function test_active_products_are_in_the_sitemap(): void
    {
        $this->makeProduct('Produk SEO', 'produk-seo');

        SitemapController::forgetCache();
        Cache::flush();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(url('/id/products'))
            ->assertSee(url('/id/products/produk-seo'));
    }

    public function test_admin_can_manage_affiliate_products(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-products')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/kelola/affiliate-products/create')
            ->assertOk()
            ->assertSee('Pilih gambar')
            ->assertDontSee('Cari nama gambar...')
            ->assertDontSee('Unggah berkas')
            ->assertDontSee('name="data[image_id]"');
    }

    public function test_selected_media_updates_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $image = app(MediaUploadService::class)->uploadFile(UploadedFile::fake()->image('affiliate-product.jpg'));

        Livewire::test(CreateAffiliateProduct::class)
            ->dispatch('affiliate-image-picked', mediaId: $image->id)
            ->assertSet('data.image_id', $image->id);

        $product = $this->makeProduct('Produk untuk edit', 'produk-untuk-edit');

        Livewire::test(EditAffiliateProduct::class, ['record' => $product->getKey()])
            ->dispatch('affiliate-image-picked', mediaId: $image->id)
            ->assertSet('data.image_id', $image->id)
            ->dispatch('affiliate-image-picked', mediaId: null)
            ->assertSet('data.image_id', null);
    }

    public function test_affiliate_product_seeder_adds_safe_inactive_examples_and_is_idempotent(): void
    {
        $this->seed(AffiliateProductSeeder::class);

        $this->assertSame(13, AffiliateProduct::query()->count());
        $this->assertEqualsCanonicalizing(
            ['shopee', 'tiktok_shop', 'amazon', 'vps', 'other'],
            AffiliateProduct::query()->distinct()->pluck('platform')->all(),
        );
        $this->assertSame(0, AffiliateProduct::query()->where('is_active', true)->count());

        $product = AffiliateProduct::query()->where('slug', 'contoh-headphone-bluetooth-shopee')->firstOrFail();
        $product->update(['title' => 'Produk saya', 'is_active' => true]);

        $this->seed(AffiliateProductSeeder::class);

        $this->assertSame(13, AffiliateProduct::query()->count());
        $this->assertSame('Produk saya', $product->fresh()->title);
        $this->assertTrue($product->fresh()->is_active);
    }

    private function makeProduct(
        string $title,
        string $slug,
        string $platform = 'shopee',
        string $category = 'Umum',
        bool $active = true,
    ): AffiliateProduct {
        return AffiliateProduct::query()->create([
            'title' => $title,
            'slug' => $slug,
            'category' => $category,
            'platform' => $platform,
            'affiliate_url' => 'https://shopee.co.id/product/123',
            'price' => 100000,
            'is_active' => $active,
        ]);
    }
}

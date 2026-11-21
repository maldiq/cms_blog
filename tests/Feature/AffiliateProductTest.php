<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\CreateAffiliateProduct;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\EditAffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\Seo\Http\Controllers\SitemapController;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
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
            AffiliateCategorySeeder::class,
            AffiliatePlatformSeeder::class,
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

        $this->get('/id/products?platform=shopee&category=elektronik')
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
            ->assertSee(url('/id/products/produk-seo'))
            ->assertSee(url('/en/products/produk-seo'))
            ->assertSee(url('/id/kategori/umum'))
            ->assertSee(url('/en/category/general'));
    }

    public function test_empty_affiliate_category_is_noindex_and_hidden_from_navigation(): void
    {
        $this->makeProduct('Produk SEO', 'produk-seo', 'shopee', 'Elektronik');

        $this->get('/id/kategori/software')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/id/kategori/elektronik')
            ->assertOk()
            ->assertDontSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/id')
            ->assertOk()
            ->assertDontSee(url('/id/kategori/software'), false);
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

    public function test_admin_can_manage_affiliate_categories_and_platforms(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        app()->setLocale('en');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-categories')
            ->assertOk()
            ->assertSee('Electronics');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-platforms')
            ->assertOk()
            ->assertSee('Shopee');

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'hosting-vps'))
            ->firstOrFail();

        $this->actingAs($admin)
            ->get("/kelola/affiliate-categories/{$category->getKey()}/edit")
            ->assertOk()
            ->assertSee('Intro kategori');

        Livewire::actingAs($admin)
            ->test(CreateAffiliateProduct::class)
            ->assertFormFieldExists('affiliate_category_id')
            ->assertFormFieldExists('affiliate_platform_id')
            ->assertFormFieldExists('is_hosting');
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

    public function test_affiliate_product_seeder_adds_curated_catalog_and_is_idempotent(): void
    {
        $this->seed(AffiliateProductSeeder::class);

        $this->assertSame(46, AffiliateProduct::query()->count());
        $this->assertEqualsCanonicalizing(
            ['shopee', 'tiktok_shop', 'vps'],
            AffiliateProduct::query()->with('affiliatePlatform')->get()->pluck('affiliatePlatform.slug')->unique()->all(),
        );
        $this->assertSame(46, AffiliateProduct::query()->where('is_active', true)->count());
        $this->assertSame(2, AffiliateProduct::query()->where('is_hosting', true)->count());

        $product = AffiliateProduct::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'wardah-cushion-series'))
            ->firstOrFail();
        $product->translateOrNew('id')->fill(['title' => 'Produk saya'])->save();
        $product->update(['is_active' => false]);

        $this->seed(AffiliateProductSeeder::class);

        $this->assertSame(46, AffiliateProduct::query()->count());
        $this->assertSame('Produk saya', $product->fresh()->translate('id', false)?->title);
        $this->assertFalse((bool) $product->fresh()->is_active);
    }

    public function test_affiliate_product_seeder_removes_untouched_legacy_demo_rows(): void
    {
        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('locale', 'id')->where('name', 'Umum'))
            ->firstOrFail();
        $platform = AffiliatePlatform::query()->where('slug', 'shopee')->firstOrFail();

        $legacy = AffiliateProduct::query()->create([
            'affiliate_category_id' => $category->getKey(),
            'affiliate_platform_id' => $platform->getKey(),
            'affiliate_url' => 'https://shopee.co.id/',
            'price' => 100000,
            'is_active' => true,
        ]);

        $legacy->translateOrNew('id')->fill([
            'title' => 'Contoh: Headphone Bluetooth',
            'slug' => 'contoh-headphone-bluetooth-shopee',
            'description' => 'Data contoh untuk katalog Shopee. Ganti dengan produk dan tautan affiliate milik Anda.',
        ])->save();

        $this->seed(AffiliateProductSeeder::class);

        $this->assertDatabaseMissing('affiliate_products', ['id' => $legacy->getKey()]);
        $this->assertSame(46, AffiliateProduct::query()->count());
    }

    public function test_product_flags_marketplace_search_links_as_placeholder(): void
    {
        $product = $this->makeProduct('Produk Placeholder', 'produk-placeholder');

        $product->update(['affiliate_url' => 'https://shopee.co.id/search?keyword=wardah']);
        $this->assertTrue($product->fresh()->usesPlaceholderSearchLink());

        $product->update(['affiliate_url' => 'https://www.tiktok.com/search?q=wardah']);
        $this->assertTrue($product->fresh()->usesPlaceholderSearchLink());

        $product->update(['affiliate_url' => 'https://s.shopee.co.id/abc123']);
        $this->assertFalse($product->fresh()->usesPlaceholderSearchLink());

        $product->update(['affiliate_url' => 'https://www.tiktok.com/@toko/video/123']);
        $this->assertFalse($product->fresh()->usesPlaceholderSearchLink());
    }

    public function test_admin_can_update_product_translations(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $product = $this->makeProduct('Judul Lama', 'judul-lama');

        Livewire::test(EditAffiliateProduct::class, ['record' => $product->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => [
                        'title' => 'Judul Baru',
                        'slug' => 'judul-baru',
                        'description' => 'Deskripsi baru',
                    ],
                    'en' => [
                        'title' => 'New Title',
                        'slug' => 'new-title',
                        'description' => 'New description',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $product = $product->fresh();

        $this->assertSame('Judul Baru', $product->translate('id', false)?->title);
        $this->assertSame('judul-baru', $product->translate('id', false)?->slug);
        $this->assertSame('New Title', $product->translate('en', false)?->title);
        $this->assertSame('new-title', $product->translate('en', false)?->slug);
    }

    public function test_hosting_category_shows_spec_table_faq_and_schema(): void
    {
        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('locale', 'id')->where('slug', 'hosting-vps'))
            ->firstOrFail();

        $category->translateOrNew('id')->fill([
            'intro' => '<p>Panduan memilih VPS untuk website kecil.</p><p><strong>Fokus pada RAM</strong> dan bandwidth.</p>',
            'meta_title' => 'Hosting & VPS untuk Website Kecil',
            'meta_description' => 'Bandingkan paket VPS berdasarkan RAM, vCPU, dan bandwidth.',
            'faq' => [
                ['question' => 'Berapa RAM untuk VPS website?', 'answer' => 'Satu sampai dua GB biasanya cukup.'],
            ],
        ])->save();

        $this->makeProduct('Hostinger VPS KVM 1', 'hostinger-vps-kvm-1', 'vps', 'Hosting & VPS', true, [
            'is_hosting' => true,
            'billing_period' => 'monthly',
            'cpu_cores' => 1,
            'ram_mb' => 4096,
            'storage_gb' => 50,
            'storage_type' => 'NVMe',
            'bandwidth_tb' => 4,
            'ipv4_count' => 1,
            'ipv6_support' => true,
            'port_speed' => '1Gbps',
            'location' => 'Jakarta',
            'last_checked_at' => '2026-11-20',
        ]);

        $this->get('/id/kategori/hosting-vps')
            ->assertOk()
            ->assertSee('Panduan memilih VPS untuk website kecil')
            ->assertSee('<strong>Fokus pada RAM</strong>', false)
            ->assertSee('Spesifikasi &amp; harga', false)
            ->assertSee('1 vCore')
            ->assertSee('4 GB')
            ->assertSee('50 GB NVMe')
            ->assertSee('4 TB')
            ->assertSee('1Gbps')
            ->assertSee('Jakarta')
            ->assertSee('Rp 100.000')
            ->assertSee('/bulan')
            ->assertSee('Berapa RAM untuk VPS website?')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('"@type":"ItemList"', false)
            ->assertDontSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/id/products/hostinger-vps-kvm-1')
            ->assertOk()
            ->assertSee('Spesifikasi &amp; harga', false)
            ->assertSee('50 GB NVMe')
            ->assertSee('Terakhir diperbarui');
    }

    public function test_non_hosting_product_does_not_render_vps_specs(): void
    {
        $this->makeProduct('Headphone Biasa', 'headphone-biasa', 'shopee', 'Elektronik', true, [
            'ipv6_support' => false,
        ]);

        $this->get('/id/products/headphone-biasa')
            ->assertOk()
            ->assertDontSee('Spesifikasi &amp; harga', false)
            ->assertDontSee('IPv6');

        $this->get('/id/kategori/elektronik')
            ->assertOk()
            ->assertDontSee('Spesifikasi &amp; harga', false);
    }

    private function makeProduct(
        string $title,
        string $slug,
        string $platform = 'shopee',
        string $category = 'Umum',
        bool $active = true,
        array $specs = [],
    ): AffiliateProduct {
        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'id')
                    ->where('name', $category))
                ->value('id'),
            'affiliate_platform_id' => AffiliatePlatform::query()->where('slug', $platform)->value('id'),
            'affiliate_url' => 'https://shopee.co.id/product/123',
            'price' => 100000,
            'is_active' => $active,
            ...$specs,
        ]);

        foreach (['id', 'en'] as $locale) {
            $product->translateOrNew($locale)->fill([
                'title' => $title,
                'slug' => $slug,
                'description' => 'Deskripsi produk uji.',
            ])->save();
        }

        return $product;
    }
}

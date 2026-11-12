<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateStorefrontTest extends TestCase
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

    public function test_home_shows_hero_slider_categories_featured_and_new_products(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'shopee');

        $this->makeProduct('Produk Unggulan Terbaik', 'produk-unggulan-terbaik', $category, $platform, [
            'is_featured' => true,
            'price' => 250000,
            'original_price' => 300000,
        ]);
        $this->makeProduct('Produk Baru Minggu Ini', 'produk-baru-minggu-ini', $category, $platform, [
            'price' => 100000,
        ]);

        $banner = AffiliateBanner::query()->create([
            'theme' => 'accent',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $banner->translateOrNew('id')->fill([
            'title' => 'Promo Spesial Akhir Pekan',
            'subtitle' => 'Diskon pilihan dari marketplace favorit.',
            'badge' => 'Promo',
            'cta_label' => 'Lihat Promo',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Promo Spesial Akhir Pekan')
            ->assertSee('Diskon pilihan dari marketplace favorit.')
            ->assertSee('Pilihan NainPicks')
            ->assertSee('Produk Baru')
            ->assertSee('Belanja sesuai kebutuhan')
            ->assertSee('Produk Unggulan Terbaik')
            ->assertSee('Produk Baru Minggu Ini')
            ->assertSee('Rp 250.000');
    }

    public function test_home_uses_config_slides_when_no_banner_exists(): void
    {
        $fallbackTitle = config('affiliate.hero.fallback_slides.0.title');

        $this->assertNotEmpty($fallbackTitle);

        $this->get('/id')
            ->assertOk()
            ->assertSee($fallbackTitle)
            ->assertSee('Produk Baru');

        $this->get('/en')
            ->assertOk()
            ->assertSee(config('affiliate.hero.fallback_slides_en.0.title'));
    }

    public function test_category_page_only_shows_products_from_that_category(): void
    {
        [$electronics, $shopee] = $this->taxonomy('Elektronik', 'shopee');
        [$kitchen] = $this->taxonomy('Dapur', 'shopee');

        $this->makeProduct('Headphone Keren', 'headphone-keren', $electronics, $shopee);
        $this->makeProduct('Wajan Anti Lengket', 'wajan-anti-lengket', $kitchen, $shopee);

        $this->get('/id/kategori/elektronik')
            ->assertOk()
            ->assertSee('Kategori Elektronik')
            ->assertSee('Headphone Keren')
            ->assertDontSee('Wajan Anti Lengket')
            ->assertSee(url('/en/category/electronics'), false)
            ->assertSee('href="/en/category/electronics"', false);

        $this->get('/en/category/electronics')
            ->assertOk()
            ->assertSee('Electronics')
            ->assertSee('Headphone Keren')
            ->assertDontSee('Wajan Anti Lengket');

        $this->get('/id/kategori/tidak-ada')->assertNotFound();
    }

    public function test_catalog_supports_sorting_and_price_filters(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'shopee');

        $this->makeProduct('AAA Produk Murah', 'aaa-produk-murah', $category, $platform, ['price' => 50000]);
        $this->makeProduct('ZZZ Produk Mahal', 'zzz-produk-mahal', $category, $platform, ['price' => 500000]);

        $this->get('/id/products?sort=price_asc')
            ->assertOk()
            ->assertSeeInOrder(['AAA Produk Murah', 'ZZZ Produk Mahal']);

        $this->get('/id/products?max_price=100000')
            ->assertOk()
            ->assertSee('AAA Produk Murah')
            ->assertDontSee('ZZZ Produk Mahal');
    }

    public function test_inactive_banners_are_not_rendered(): void
    {
        $banner = AffiliateBanner::query()->create([
            'theme' => 'brand',
            'is_active' => false,
        ]);

        $banner->translateOrNew('id')->fill([
            'title' => 'Banner Nonaktif Rahasia',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertDontSee('Banner Nonaktif Rahasia')
            ->assertSee(config('affiliate.hero.fallback_slides.0.title'));
    }

    public function test_admin_can_manage_affiliate_banners(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-banners')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/kelola/affiliate-banners/create')
            ->assertOk()
            ->assertSee('Banner Slider');
    }

    public function test_product_pages_use_the_locale_specific_title_and_slug(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'shopee');

        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => $category->getKey(),
            'affiliate_platform_id' => $platform->getKey(),
            'affiliate_url' => 'https://shopee.co.id/product/headphone',
            'price' => 150000,
            'is_active' => true,
        ]);

        $product->translateOrNew('id')->fill([
            'title' => 'Headphone Nirkabel',
            'slug' => 'headphone-nirkabel',
            'description' => 'Deskripsi dalam bahasa Indonesia.',
        ])->save();

        $product->translateOrNew('en')->fill([
            'title' => 'Wireless Headphone',
            'slug' => 'wireless-headphone',
            'description' => 'Description in English.',
        ])->save();

        $this->get('/id/products/headphone-nirkabel')
            ->assertOk()
            ->assertSee('Headphone Nirkabel')
            ->assertSee('Deskripsi dalam bahasa Indonesia.')
            ->assertSee(url('/en/products/wireless-headphone'), false)
            ->assertSee('href="/en/products/wireless-headphone"', false);

        $this->get('/en/products/wireless-headphone')
            ->assertOk()
            ->assertSee('Wireless Headphone')
            ->assertSee('Description in English.');

        $this->get('/id/products/wireless-headphone')->assertNotFound();
    }

    public function test_products_without_translation_for_locale_are_hidden(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'shopee');

        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => $category->getKey(),
            'affiliate_platform_id' => $platform->getKey(),
            'affiliate_url' => 'https://shopee.co.id/product/khusus-id',
            'is_active' => true,
        ]);

        $product->translateOrNew('id')->fill([
            'title' => 'Hanya Bahasa Indonesia',
            'slug' => 'hanya-bahasa-indonesia',
        ])->save();

        $this->get('/id/products')->assertOk()->assertSee('Hanya Bahasa Indonesia');
        $this->get('/en/products')->assertOk()->assertDontSee('Hanya Bahasa Indonesia');
    }

    public function test_banner_slides_are_localized_per_language(): void
    {
        $banner = AffiliateBanner::query()->create([
            'theme' => 'brand',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $banner->translateOrNew('id')->fill([
            'badge' => 'Promo Indonesia',
            'title' => 'Judul Banner Indonesia',
            'subtitle' => 'Deskripsi banner Indonesia.',
            'cta_label' => 'Lihat Promo',
        ])->save();

        $banner->translateOrNew('en')->fill([
            'badge' => 'English Promo',
            'title' => 'English Banner Title',
            'subtitle' => 'English banner description.',
            'cta_label' => 'View Promo',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Judul Banner Indonesia')
            ->assertDontSee('English Banner Title');

        $this->get('/en')
            ->assertOk()
            ->assertSee('English Banner Title')
            ->assertDontSee('Judul Banner Indonesia');
    }

    public function test_platform_names_follow_the_active_language(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'other');

        $platform->translateOrNew('en')->fill(['name' => 'Others'])->save();

        $this->makeProduct('Produk Platform Lokal', 'produk-platform-lokal', $category, $platform);

        $this->get('/id/products')
            ->assertOk()
            ->assertSee('Lainnya')
            ->assertDontSee('Others');

        $this->get('/en/products')
            ->assertOk()
            ->assertSee('Others');
    }

    public function test_prices_follow_the_locale_currency(): void
    {
        [$category, $platform] = $this->taxonomy('Elektronik', 'shopee');

        $this->makeProduct('Produk Harga Lokal', 'produk-harga-lokal', $category, $platform, [
            'price' => 160000,
            'original_price' => 320000,
        ]);

        $this->get('/id/products')
            ->assertOk()
            ->assertSee('Rp 160.000')
            ->assertSee('Rp 320.000');

        $this->get('/en/products')
            ->assertOk()
            ->assertSee('$10.00')
            ->assertSee('$20.00');
    }

    /**
     * @return array{0: AffiliateCategory, 1: AffiliatePlatform}
     */
    private function taxonomy(string $categoryName, string $platformSlug): array
    {
        return [
            AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'id')
                    ->where('name', $categoryName))
                ->firstOrFail(),
            AffiliatePlatform::query()->where('slug', $platformSlug)->firstOrFail(),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeProduct(
        string $title,
        string $slug,
        AffiliateCategory $category,
        AffiliatePlatform $platform,
        array $overrides = [],
    ): AffiliateProduct {
        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => $category->getKey(),
            'affiliate_platform_id' => $platform->getKey(),
            'affiliate_url' => 'https://shopee.co.id/product/'.$slug,
            'price' => 100000,
            'is_active' => true,
            ...$overrides,
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

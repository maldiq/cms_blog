<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\User\Models\User;
use App\Filament\Widgets\AffiliateStatsOverview;
use App\Filament\Widgets\LatestAffiliateProductsTable;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
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

    public function test_dashboard_renders_affiliate_overview_widgets(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'elektronik'))
            ->firstOrFail();
        $platform = AffiliatePlatform::query()->where('slug', 'shopee')->firstOrFail();

        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => $category->getKey(),
            'affiliate_platform_id' => $platform->getKey(),
            'affiliate_url' => 'https://shopee.co.id/product/1',
            'price' => 160000,
            'click_count' => 12,
            'is_active' => true,
        ]);

        $product->translateOrNew('id')->fill([
            'title' => 'Produk Dashboard',
            'slug' => 'produk-dashboard',
        ])->save();

        $this->actingAs($admin)
            ->get('/kelola')
            ->assertOk()
            ->assertSee('affiliate-stats-overview')
            ->assertSee('affiliate-products-by-category-chart')
            ->assertSee('affiliate-clicks-by-platform-chart')
            ->assertSee('latest-affiliate-products-table');

        Livewire::test(AffiliateStatsOverview::class)
            ->assertSee('Produk Aktif')
            ->assertSee('Total Klik Affiliate')
            ->assertSee('Produk Unggulan')
            ->assertSee('Kategori Aktif');

        Livewire::test(LatestAffiliateProductsTable::class)
            ->assertSee('Produk Affiliate Terbaru')
            ->assertSee('Produk Dashboard');
    }
}

<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Filament\Pages\AffiliateLinkBuilder;
use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource\Pages\CreateAffiliateTrafficSource;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliateClick;
use App\Domain\Affiliate\Models\AffiliateDestination;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use App\Domain\Affiliate\Services\AffiliateClickReportService;
use App\Domain\Affiliate\Services\TrafficSource;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\AffiliateTrafficSourceSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateTrackingTest extends TestCase
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
            AffiliateTrafficSourceSeeder::class,
        ]);

        AffiliateTrafficSource::flushLabelCache();
    }

    public function test_root_captures_and_normalizes_source_before_locale_redirect(): void
    {
        $this->get('/?src=FB%20Grup%20Gadget')
            ->assertStatus(302);

        $this->assertSame('fb_grup_gadget', session(TrafficSource::SESSION_LAST));
        $this->assertSame('fb_grup_gadget', session(TrafficSource::SESSION_FIRST));
    }

    public function test_redirect_logs_click_and_appends_last_touch_source_to_shopee(): void
    {
        $product = $this->makeProduct(
            'Headphone Uji',
            'headphone-uji',
            'shopee',
            'https://shopee.co.id/product/123?utm_source=fb#detail',
        );

        $response = $this->withSession([
            TrafficSource::SESSION_LAST => 'fb_grup_gadget',
            TrafficSource::SESSION_FIRST => 'fb_reels',
        ])->get('/id/go/headphone-uji');

        $response->assertStatus(302);

        $location = (string) $response->headers->get('Location');

        $this->assertStringContainsString('sub_id=fb_grup_gadget', $location);
        $this->assertStringContainsString('utm_source=fb', $location);
        $this->assertStringEndsWith('#detail', $location);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));

        $click = AffiliateClick::query()->sole();

        $this->assertSame($product->getKey(), $click->affiliate_product_id);
        $this->assertSame('fb_grup_gadget', $click->source);
        $this->assertSame('fb_reels', $click->first_source);
        $this->assertSame('id', $click->locale);
        $this->assertSame(26, strlen($click->click_id));
        $this->assertNotNull($click->ip_hash);
        $this->assertNotSame('127.0.0.1', $click->ip_hash);
        $this->assertSame(1, $product->fresh()->click_count);
    }

    public function test_redirect_without_source_logs_direct_and_keeps_url_unchanged(): void
    {
        $product = $this->makeProduct('Kabel USB', 'kabel-usb');

        $this->get('/id/go/kabel-usb')
            ->assertRedirect('https://shopee.co.id/product/123');

        $click = AffiliateClick::query()->sole();

        $this->assertSame(AffiliateClick::SOURCE_DIRECT, $click->source);
        $this->assertNull($click->first_source);
        $this->assertSame(1, $product->fresh()->click_count);
    }

    public function test_amazon_uses_ascsubtag_and_preserves_associate_tag(): void
    {
        $this->makeProduct(
            'Buku Amazon',
            'buku-amazon',
            'amazon',
            'https://www.amazon.com/dp/B000?tag=myaff-20',
        );

        $response = $this->withSession([
            TrafficSource::SESSION_LAST => 'fb_reels',
        ])->get('/id/go/buku-amazon');

        $location = (string) $response->headers->get('Location');

        $this->assertStringContainsString('ascsubtag=fb_reels', $location);
        $this->assertStringContainsString('tag=myaff-20', $location);
    }

    public function test_bot_and_link_preview_requests_are_not_logged(): void
    {
        $product = $this->makeProduct('Produk Bot', 'produk-bot');

        $this->withHeaders(['User-Agent' => 'facebookexternalhit/1.1'])
            ->get('/id/go/produk-bot')
            ->assertStatus(302);

        $this->assertSame(0, AffiliateClick::query()->count());
        $this->assertSame(0, $product->fresh()->click_count);
    }

    public function test_out_of_stock_primary_destination_falls_back_to_active_secondary(): void
    {
        $product = $this->makeProduct('Produk Cadangan', 'produk-cadangan', 'shopee', 'https://shopee.co.id/primary');

        $primary = $product->destinations()->where('is_primary', true)->firstOrFail();
        $primary->update(['status' => AffiliateDestination::STATUS_OUT_OF_STOCK]);

        $secondary = $product->destinations()->create([
            'label' => 'Toko cadangan',
            'url' => 'https://shopee.co.id/secondary',
            'status' => AffiliateDestination::STATUS_ACTIVE,
            'is_primary' => false,
            'priority' => 10,
        ]);

        $response = $this->withSession([
            TrafficSource::SESSION_LAST => 'fb_grup_promo',
        ])->get('/id/go/produk-cadangan');

        $location = (string) $response->headers->get('Location');

        $this->assertStringContainsString('shopee.co.id/secondary', $location);
        $this->assertStringContainsString('sub_id=fb_grup_promo', $location);
        $this->assertSame($secondary->getKey(), AffiliateClick::query()->sole()->affiliate_destination_id);
    }

    public function test_all_inactive_destinations_redirect_back_to_product_with_message(): void
    {
        $product = $this->makeProduct('Produk Habis', 'produk-habis');

        $product->destinations()->update(['status' => AffiliateDestination::STATUS_PAUSED]);

        $this->get('/id/go/produk-habis')
            ->assertRedirect('/id/products/produk-habis')
            ->assertSessionHas('affiliate_link_unavailable');

        $this->assertSame(0, AffiliateClick::query()->count());
    }

    public function test_destination_changes_are_written_to_audit_trail(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->actingAs($user);

        $product = $this->makeProduct('Produk Audit', 'produk-audit');
        $destination = $product->destinations()->where('is_primary', true)->firstOrFail();

        $destination->update(['url' => 'https://shopee.co.id/baru']);

        $audit = $destination->audits()->where('action', 'updated')->firstOrFail();

        $this->assertSame($user->getKey(), $audit->user_id);
        $this->assertSame('https://shopee.co.id/product/123', $audit->changes['url']['old']);
        $this->assertSame('https://shopee.co.id/baru', $audit->changes['url']['new']);
    }

    public function test_click_report_aggregates_by_source_product_and_locale(): void
    {
        $productA = $this->makeProduct('Produk A', 'produk-a');
        $productB = $this->makeProduct('Produk B', 'produk-b');

        $this->makeClick($productA, 'fb_grup_gadget', 'id');
        $this->makeClick($productA, 'fb_grup_gadget', 'id');
        $this->makeClick($productB, 'fb_reels', 'en');

        $service = app(AffiliateClickReportService::class);
        $summary = $service->summary(30);

        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $summary['sources']);
        $this->assertSame('fb_grup_gadget', $summary['top_source']);
        $this->assertSame('Produk A', $summary['top_product']);

        $this->assertSame('fb_grup_gadget', $service->bySource(30)->first()->source_label);
        $this->assertSame(2, (int) $service->bySource(30)->first()->clicks);
        $this->assertSame('id', $service->byLocale(30)->first()->locale);
    }

    public function test_privacy_page_explains_traffic_source_tracking(): void
    {
        $this->get('/id/privacy')
            ->assertOk()
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Pelacakan sumber kunjungan')
            ->assertSee('cookie first-party');
    }

    public function test_admin_can_open_click_report_link_builder_and_product_destinations(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $product = $this->makeProduct('Produk Admin', 'produk-admin');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-clicks')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/kelola/affiliate-click-report')
            ->assertOk()
            ->assertSee('Klik per sumber');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-link-builder')
            ->assertOk()
            ->assertSee('Kode sumber');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-products/'.$product->getKey().'/edit')
            ->assertOk()
            // Relation manager dimuat lazy oleh Filament, jadi yang dicek
            // adalah penanda komponennya di halaman edit.
            ->assertSee('destinations-relation-manager');
    }

    public function test_link_builder_generates_share_url_and_smart_link(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $product = $this->makeProduct('Produk Link', 'produk-link');

        Livewire::test(AffiliateLinkBuilder::class)
            ->fillForm([
                'source' => 'fb_reels',
                'locale' => 'id',
                'target' => 'home',
            ])
            ->call('generate')
            ->assertHasNoFormErrors()
            ->assertSet('data.generated_url', route('home', ['locale' => 'id']).'?src=fb_reels');

        Livewire::test(AffiliateLinkBuilder::class)
            ->fillForm([
                'source' => 'fb_grup_gadget',
                'locale' => 'id',
                'target' => 'smart_link',
                'product_id' => $product->getKey(),
            ])
            ->call('generate')
            ->assertHasNoFormErrors()
            ->assertSet(
                'data.generated_url',
                route('products.redirect', ['locale' => 'id', 'slug' => 'produk-link']).'?src=fb_grup_gadget',
            );
    }

    public function test_destination_check_command_records_status_without_changing_manual_status(): void
    {
        Http::fake([
            'https://shopee.co.id/mati*' => Http::response('', 404),
            'https://shopee.co.id/*' => Http::response('', 200),
        ]);

        $product = $this->makeProduct('Produk Cek', 'produk-cek');
        $primary = $product->destinations()->where('is_primary', true)->firstOrFail();

        $this->artisan('affiliate:check-destinations')
            ->assertSuccessful();

        $primary->refresh();

        $this->assertSame(200, $primary->last_check_status);
        $this->assertNotNull($primary->last_checked_at);
        $this->assertSame(AffiliateDestination::STATUS_ACTIVE, $primary->status);
    }

    public function test_prune_command_removes_clicks_older_than_retention(): void
    {
        $product = $this->makeProduct('Produk Prune', 'produk-prune');
        $old = $this->makeClick($product, 'fb_reels', 'id');
        $old->update(['clicked_at' => now()->subDays(120)]);
        $fresh = $this->makeClick($product, 'fb_reels', 'id');

        $this->artisan('affiliate:prune-clicks')
            ->assertSuccessful();

        $this->assertNull($old->fresh());
        $this->assertNotNull($fresh->fresh());
    }

    public function test_admin_can_create_traffic_source_and_it_appears_in_link_builder(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreateAffiliateTrafficSource::class)
            ->fillForm([
                'code' => 'fb_test',
                'label' => 'FB Test',
                'is_active' => true,
                'sort_order' => 5,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('affiliate_traffic_sources', [
            'code' => 'fb_test',
            'label' => 'FB Test',
        ]);

        $this->assertArrayHasKey('fb_test', AffiliateTrafficSource::codeOptions());

        Livewire::test(AffiliateLinkBuilder::class)
            ->assertFormFieldExists('saved_source')
            ->fillForm([
                'saved_source' => 'fb_test',
                'source' => 'fb_test',
                'locale' => 'id',
                'target' => 'home',
            ])
            ->call('generate')
            ->assertHasNoFormErrors()
            ->assertSet('data.generated_url', route('home', ['locale' => 'id']).'?src=fb_test');
    }

    public function test_report_and_click_list_use_saved_source_label(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        AffiliateTrafficSource::query()->create([
            'code' => 'fb_label',
            'label' => 'Grup Berlabel',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = $this->makeProduct('Produk Label', 'produk-label');
        $this->makeClick($product, 'fb_label', 'id');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-click-report')
            ->assertOk()
            ->assertSee('Grup Berlabel');

        $this->actingAs($admin)
            ->get('/kelola/affiliate-clicks')
            ->assertOk()
            ->assertSee('Grup Berlabel');
    }

    private function makeProduct(
        string $title,
        string $slug,
        string $platform = 'shopee',
        string $url = 'https://shopee.co.id/product/123',
    ): AffiliateProduct {
        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'id')
                    ->where('name', 'Umum'))
                ->value('id'),
            'affiliate_platform_id' => AffiliatePlatform::query()->where('slug', $platform)->value('id'),
            'affiliate_url' => $url,
            'price' => 100000,
            'is_active' => true,
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

    private function makeClick(AffiliateProduct $product, string $source, string $locale): AffiliateClick
    {
        return AffiliateClick::query()->create([
            'click_id' => (string) Str::ulid(),
            'affiliate_product_id' => $product->getKey(),
            'affiliate_destination_id' => null,
            'locale' => $locale,
            'source' => $source,
            'first_source' => $source,
            'referrer_host' => 'facebook.com',
            'ip_hash' => hash('sha256', 'test'),
            'user_agent_family' => 'Chrome',
            'clicked_at' => now(),
        ]);
    }
}

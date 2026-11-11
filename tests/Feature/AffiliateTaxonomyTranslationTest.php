<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages\CreateAffiliateBanner;
use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages\EditAffiliateBanner;
use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages\CreateAffiliateCategory;
use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages\EditAffiliateCategory;
use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages\CreateAffiliatePlatform;
use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages\EditAffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateTaxonomyTranslationTest extends TestCase
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

    public function test_admin_can_update_category_translations(): void
    {
        $admin = $this->admin();

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'elektronik'))
            ->firstOrFail();

        Livewire::actingAs($admin)
            ->test(EditAffiliateCategory::class, ['record' => $category->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => ['name' => 'Elektronik & Gadget', 'slug' => 'elektronik-gadget'],
                    'en' => ['name' => 'Electronics & Gadgets', 'slug' => 'electronics-gadgets'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $category = $category->fresh();

        $this->assertSame('Elektronik & Gadget', $category->translate('id', false)?->name);
        $this->assertSame('elektronik-gadget', $category->translate('id', false)?->slug);
        $this->assertSame('Electronics & Gadgets', $category->translate('en', false)?->name);
        $this->assertSame('electronics-gadgets', $category->translate('en', false)?->slug);
    }

    public function test_admin_can_update_platform_translations(): void
    {
        $admin = $this->admin();

        $platform = AffiliatePlatform::query()->where('slug', 'other')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(EditAffiliatePlatform::class, ['record' => $platform->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => ['name' => 'Lainnya'],
                    'en' => ['name' => 'Other marketplaces'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $platform = $platform->fresh();

        $this->assertSame('Lainnya', $platform->translate('id', false)?->name);
        $this->assertSame('Other marketplaces', $platform->translate('en', false)?->name);
    }

    public function test_admin_can_update_banner_translations(): void
    {
        $admin = $this->admin();

        $banner = AffiliateBanner::query()->create([
            'theme' => 'brand',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $banner->translateOrNew('id')->fill([
            'badge' => 'Promo',
            'title' => 'Judul Awal',
            'subtitle' => 'Subjudul awal.',
            'cta_label' => 'Lihat',
        ])->save();

        Livewire::actingAs($admin)
            ->test(EditAffiliateBanner::class, ['record' => $banner->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => [
                        'badge' => 'Promo',
                        'title' => 'Judul Baru',
                        'subtitle' => 'Subjudul baru.',
                        'cta_label' => 'Lihat Promo',
                    ],
                    'en' => [
                        'badge' => 'Promo',
                        'title' => 'New Title',
                        'subtitle' => 'New subtitle.',
                        'cta_label' => 'View Promo',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $banner = $banner->fresh();

        $this->assertSame('Judul Baru', $banner->translate('id', false)?->title);
        $this->assertSame('New Title', $banner->translate('en', false)?->title);
        $this->assertSame('View Promo', $banner->translate('en', false)?->cta_label);
    }

    public function test_admin_can_create_category_with_translations(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateAffiliateCategory::class)
            ->fillForm([
                'translations' => [
                    'id' => ['name' => 'Mainan', 'slug' => 'mainan'],
                    'en' => ['name' => 'Toys', 'slug' => 'toys'],
                ],
                'is_active' => true,
                'sort_order' => 9,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query->where('slug', 'toys'))
            ->firstOrFail();

        $this->assertSame('Mainan', $category->translate('id', false)?->name);
        $this->assertSame('Toys', $category->translate('en', false)?->name);
    }

    public function test_admin_can_create_platform_with_translations(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateAffiliatePlatform::class)
            ->fillForm([
                'translations' => [
                    'id' => ['name' => 'Etsy'],
                    'en' => ['name' => 'Etsy Marketplace'],
                ],
                'slug' => 'etsy',
                'is_active' => true,
                'sort_order' => 6,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $platform = AffiliatePlatform::query()->where('slug', 'etsy')->firstOrFail();

        $this->assertSame('Etsy', $platform->translate('id', false)?->name);
        $this->assertSame('Etsy Marketplace', $platform->translate('en', false)?->name);
    }

    public function test_admin_can_create_banner_with_translations(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(CreateAffiliateBanner::class)
            ->fillForm([
                'translations' => [
                    'id' => [
                        'badge' => 'Baru',
                        'title' => 'Banner Baru',
                        'subtitle' => 'Subjudul banner baru.',
                        'cta_label' => 'Lihat',
                    ],
                    'en' => [
                        'badge' => 'New',
                        'title' => 'New Banner',
                        'subtitle' => 'New banner subtitle.',
                        'cta_label' => 'View',
                    ],
                ],
                'theme' => 'brand',
                'is_active' => true,
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $banner = AffiliateBanner::query()
            ->whereHas('translations', fn ($query) => $query->where('title', 'New Banner'))
            ->firstOrFail();

        $this->assertSame('Banner Baru', $banner->translate('id', false)?->title);
        $this->assertSame('New Banner', $banner->translate('en', false)?->title);
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return $admin;
    }
}

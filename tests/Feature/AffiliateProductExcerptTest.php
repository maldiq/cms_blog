<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\EditAffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateProductExcerptTest extends TestCase
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

    public function test_admin_can_save_excerpt_and_it_is_loaded_back_into_the_form(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $product = $this->makeProduct('Headphone Uji', 'headphone-uji');

        Livewire::test(EditAffiliateProduct::class, ['record' => $product->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => [
                        'title' => 'Headphone Uji',
                        'slug' => 'headphone-uji',
                        'excerpt' => 'Ringkasan singkat produk.',
                        'description' => 'Deskripsi lengkap produk.',
                    ],
                    'en' => [
                        'title' => 'Test Headphone',
                        'slug' => 'test-headphone',
                        'excerpt' => 'Short product summary.',
                        'description' => 'Full product description.',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $product = $product->fresh();

        $this->assertSame('Ringkasan singkat produk.', $product->translate('id', false)?->excerpt);
        $this->assertSame('Short product summary.', $product->translate('en', false)?->excerpt);

        Livewire::test(EditAffiliateProduct::class, ['record' => $product->getKey()])
            ->assertFormSet([
                'translations' => [
                    'id' => ['excerpt' => 'Ringkasan singkat produk.'],
                    'en' => ['excerpt' => 'Short product summary.'],
                ],
            ]);
    }

    public function test_detail_page_shows_excerpt_above_the_buy_button(): void
    {
        $this->makeProduct(
            'Kamera Mini',
            'kamera-mini',
            excerpt: 'Kamera ringkas untuk pemula.',
            description: 'Deskripsi detail kamera mini yang panjang.',
        );

        $html = $this->get('/id/products/kamera-mini')->assertOk()->getContent();
        $body = substr($html, (int) strpos($html, '<body'));

        $this->assertSame(1, substr_count($body, 'Kamera ringkas untuk pemula.'));
        $this->assertSame(1, substr_count($body, 'Deskripsi detail kamera mini yang panjang.'));

        $excerptPosition = strpos($body, 'Kamera ringkas untuk pemula.');
        $buyButtonPosition = strpos($body, '/id/go/kamera-mini');
        $descriptionHeadingPosition = strpos($body, 'Deskripsi produk');

        $this->assertNotFalse($excerptPosition);
        $this->assertNotFalse($buyButtonPosition);
        $this->assertNotFalse($descriptionHeadingPosition);

        // Excerpt tampil sebelum tombol beli, deskripsi hanya di bagian bawah.
        $this->assertLessThan($buyButtonPosition, $excerptPosition);
        $this->assertGreaterThan($buyButtonPosition, $descriptionHeadingPosition);
        $this->assertGreaterThan($descriptionHeadingPosition, strpos($body, 'Deskripsi detail kamera mini yang panjang.'));
    }

    public function test_detail_page_without_excerpt_does_not_duplicate_the_description(): void
    {
        $this->makeProduct(
            'Lampu Meja',
            'lampu-meja',
            excerpt: null,
            description: 'Deskripsi lampu meja.',
        );

        $html = $this->get('/id/products/lampu-meja')->assertOk()->getContent();
        $body = substr($html, (int) strpos($html, '<body'));

        $this->assertSame(1, substr_count($body, 'Deskripsi lampu meja.'));
        $this->assertGreaterThan(
            strpos($body, '/id/go/lampu-meja'),
            strpos($body, 'Deskripsi lampu meja.'),
        );
    }

    public function test_meta_description_prefers_the_excerpt(): void
    {
        $this->makeProduct(
            'Speaker Portabel',
            'speaker-portabel',
            excerpt: 'Speaker kecil dengan suara besar.',
            description: 'Deskripsi speaker portabel yang panjang sekali.',
        );

        $html = $this->get('/id/products/speaker-portabel')->assertOk()->getContent();

        $this->assertStringContainsString(
            '<meta name="description" content="Speaker kecil dengan suara besar.">',
            $html,
        );
        $this->assertStringNotContainsString(
            '<meta name="description" content="Deskripsi speaker portabel yang panjang sekali.">',
            $html,
        );
    }

    private function makeProduct(
        string $title,
        string $slug,
        ?string $excerpt = null,
        ?string $description = 'Deskripsi produk uji.',
    ): AffiliateProduct {
        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query
                    ->where('locale', 'id')
                    ->where('name', 'Umum'))
                ->value('id'),
            'affiliate_platform_id' => AffiliatePlatform::query()->where('slug', 'shopee')->value('id'),
            'affiliate_url' => 'https://shopee.co.id/product/123',
            'price' => 100000,
            'is_active' => true,
        ]);

        foreach (['id', 'en'] as $locale) {
            $product->translateOrNew($locale)->fill([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'description' => $description,
            ])->save();
        }

        return $product;
    }
}

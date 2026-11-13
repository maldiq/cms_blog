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

/**
 * Deskripsi produk affiliate memakai editor kaya (TipTap) seperti konten post,
 * jadi harus bisa menyimpan dan menampilkan HTML termasuk tabel.
 */
class AffiliateProductDescriptionEditorTest extends TestCase
{
    use RefreshDatabase;

    private const TABLE_HTML = '<p>Spesifikasi utama</p><table><tbody><tr><th><p>Baterai</p></th><th><p>5000 mAh</p></th></tr></tbody></table>';

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

    public function test_admin_can_save_rich_description_with_table(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $product = $this->makeProduct('Powerbank Uji', 'powerbank-uji');

        Livewire::test(EditAffiliateProduct::class, ['record' => $product->getKey()])
            ->fillForm([
                'translations' => [
                    'id' => [
                        'title' => 'Powerbank Uji',
                        'slug' => 'powerbank-uji',
                        'description' => self::TABLE_HTML,
                    ],
                    'en' => [
                        'title' => 'Test Powerbank',
                        'slug' => 'test-powerbank',
                        'description' => '<p>Main specs</p>',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $content = (string) $product->fresh()->translate('id', false)?->description;

        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('5000 mAh', $content);
    }

    public function test_detail_page_renders_rich_description_as_html(): void
    {
        $this->makeProduct('Powerbank Tampil', 'powerbank-tampil', self::TABLE_HTML);

        $html = $this->get('/id/products/powerbank-tampil')->assertOk()->getContent();
        $body = substr($html, (int) strpos($html, '<body'));

        $this->assertStringContainsString('<table', $body);
        $this->assertStringContainsString('5000 mAh', $body);
        $this->assertStringNotContainsString('&lt;table', $body);
    }

    public function test_detail_page_still_renders_legacy_plain_text_description(): void
    {
        $this->makeProduct('Lampu Lama', 'lampu-lama', "Baris satu.\nBaris dua.");

        $html = $this->get('/id/products/lampu-lama')->assertOk()->getContent();
        $body = substr($html, (int) strpos($html, '<body'));

        $this->assertStringContainsString('Baris satu.<br />', $body);
        $this->assertStringContainsString('Baris dua.', $body);
    }

    public function test_meta_description_strips_html_from_the_description(): void
    {
        $this->makeProduct(
            'Produk SEO',
            'produk-seo-rich',
            '<p>Halo <strong>dunia</strong> ini <em>produk</em>.</p>',
        );

        $html = $this->get('/id/products/produk-seo-rich')->assertOk()->getContent();

        $this->assertStringContainsString(
            '<meta name="description" content="Halo dunia ini produk.">',
            $html,
        );
        $this->assertStringNotContainsString(
            '<meta name="description" content="<p>',
            $html,
        );
    }

    private function makeProduct(
        string $title,
        string $slug,
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
                'description' => $description,
            ])->save();
        }

        return $product;
    }
}

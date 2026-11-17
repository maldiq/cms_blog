<?php

namespace Tests\Feature;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliateDestination;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Services\AffiliateProductLinkImporter;
use App\Domain\Media\Models\Media;
use App\Domain\User\Models\User;
use Database\Seeders\AffiliateCategorySeeder;
use Database\Seeders\AffiliatePlatformSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AffiliateProductLinkImportTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
            AffiliateCategorySeeder::class,
            AffiliatePlatformSeeder::class,
        ]);

        $this->directory = storage_path('app/testing/imports');

        File::ensureDirectoryExists($this->directory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_import_file_updates_link_price_and_image(): void
    {
        $product = $this->makeProduct('Wardah Cushion Series', 'wardah-cushion-series');

        $image = imagecreatetruecolor(8, 8);
        imagefill($image, 0, 0, imagecolorallocate($image, 20, 120, 200));
        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        Http::fake([
            'https://cdn.example.com/wardah.png' => Http::response($png, 200, ['Content-Type' => 'image/png']),
        ]);

        $path = $this->writeCsv([
            ['slug', 'affiliate_url', 'image_url', 'price', 'original_price'],
            ['wardah-cushion-series', 'https://s.shopee.co.id/abc123', 'https://cdn.example.com/wardah.png', '129.000', '159000'],
        ]);

        $result = app(AffiliateProductLinkImporter::class)->importFile($path, true);

        $this->assertSame(1, $result['updated']);
        $this->assertSame([], $result['errors'], json_encode($result['errors']));
        $this->assertSame(1, $result['images']);
        $this->assertSame(0, $result['skipped']);

        $product->refresh();

        $this->assertSame('https://s.shopee.co.id/abc123', $product->affiliate_url);
        $this->assertSame('129000.00', $product->price);
        $this->assertSame('159000.00', $product->original_price);
        $this->assertNotNull($product->image_id);
        $this->assertSame(1, Media::count());

        $destination = AffiliateDestination::query()
            ->where('affiliate_product_id', $product->getKey())
            ->where('is_primary', true)
            ->firstOrFail();

        $this->assertSame('https://s.shopee.co.id/abc123', $destination->url);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://cdn.example.com/wardah.png');
    }

    public function test_import_skips_invalid_url_unknown_slug_and_blank_slug(): void
    {
        $product = $this->makeProduct('Azarine Sunscreen', 'azarine-sunscreen');

        $path = $this->writeCsv([
            ['slug', 'affiliate_url'],
            ['azarine-sunscreen', 'bukan-url'],
            ['produk-tidak-ada', 'https://s.shopee.co.id/xyz'],
            ['', 'https://s.shopee.co.id/kosong'],
        ]);

        $result = app(AffiliateProductLinkImporter::class)->importFile($path, false);

        $this->assertSame(0, $result['updated']);
        $this->assertSame(3, $result['skipped']);
        $this->assertCount(3, $result['errors']);
        $this->assertSame('https://shopee.co.id/product/123', $product->fresh()->affiliate_url);
    }

    public function test_import_supports_semicolon_and_alias_headers(): void
    {
        $product = $this->makeProduct('Jisulife Fan', 'jisulife-fan');

        $path = $this->writeCsv([
            ['produk_slug', 'link', 'harga'],
            ['jisulife-fan', 'https://s.shopee.co.id/fan123', 'Rp 179.000'],
        ], ';');

        $result = app(AffiliateProductLinkImporter::class)->importFile($path, false);

        $this->assertSame(1, $result['updated']);
        $this->assertSame([], $result['errors']);
        $this->assertSame('https://s.shopee.co.id/fan123', $product->fresh()->affiliate_url);
        $this->assertSame('179000.00', $product->fresh()->price);
    }

    public function test_admin_can_open_import_page(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/kelola/affiliate-import')
            ->assertOk()
            ->assertSee('Impor Link & Gambar Produk')
            ->assertSee('Unduh template CSV');
    }

    public function test_import_command_reports_summary(): void
    {
        $this->makeProduct('Maybelline Lip', 'maybelline-lip');

        $path = $this->writeCsv([
            ['slug', 'affiliate_url'],
            ['maybelline-lip', 'https://s.shopee.co.id/lip123'],
        ]);

        $this->artisan('affiliate:import-product-links', ['file' => $path])
            ->expectsOutputToContain('1 link diperbarui')
            ->assertExitCode(0);
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private function writeCsv(array $rows, string $delimiter = ','): string
    {
        $path = $this->directory.'/import-'.uniqid().'.csv';
        $handle = fopen($path, 'wb');

        foreach ($rows as $row) {
            fputcsv($handle, $row, $delimiter, '"', '');
        }

        fclose($handle);

        return $path;
    }

    private function makeProduct(string $title, string $slug): AffiliateProduct
    {
        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query->where('locale', 'id')->where('name', 'Umum'))
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
                'description' => 'Deskripsi produk uji.',
            ])->save();
        }

        return $product;
    }
}

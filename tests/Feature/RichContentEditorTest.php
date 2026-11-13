<?php

namespace Tests\Feature;

use App\Domain\Page\Filament\Resources\PageResource\Pages\CreatePage;
use App\Domain\Page\Models\Page;
use App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages\CreatePortfolio;
use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Theme\Filament\Pages\ThemeEditor;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Memastikan editor konten (TipTap) yang dipakai di Post, Page, Portfolio,
 * dan Theme Editor benar-benar menyimpan HTML tabel.
 */
class RichContentEditorTest extends TestCase
{
    use RefreshDatabase;

    private const TABLE_HTML = '<table><tbody><tr><th><p>Spesifikasi</p></th><th><p>Nilai</p></th></tr><tr><td><p>Baterai</p></td><td><p>5000 mAh</p></td></tr></tbody></table>';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function actingAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        return $admin;
    }

    public function test_page_content_accepts_tables(): void
    {
        $admin = $this->actingAdmin();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'translations' => [
                    'id' => [
                        'title' => 'Halaman Tabel',
                        'slug' => 'halaman-tabel',
                        'content' => self::TABLE_HTML,
                    ],
                    'en' => [
                        'title' => 'Table Page',
                        'slug' => 'table-page',
                        'content' => '<p>Hello</p>',
                    ],
                ],
                'user_id' => $admin->id,
                'template' => 'default',
                'status' => Page::STATUS_DRAFT,
                'is_homepage' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::query()->firstOrFail();
        $content = (string) $page->translate('id')?->content;

        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('5000 mAh', $content);
    }

    public function test_portfolio_content_accepts_tables(): void
    {
        $this->actingAdmin();

        Livewire::test(CreatePortfolio::class)
            ->fillForm([
                'translations' => [
                    'id' => [
                        'title' => 'Portfolio Tabel',
                        'slug' => 'portfolio-tabel',
                        'excerpt' => 'Ringkasan',
                        'content' => self::TABLE_HTML,
                    ],
                    'en' => [
                        'title' => 'Table Portfolio',
                        'slug' => 'table-portfolio',
                        'excerpt' => 'Summary',
                        'content' => '<p>Hello</p>',
                    ],
                ],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $portfolio = Portfolio::query()->firstOrFail();
        $content = (string) $portfolio->translate('id')?->content;

        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('5000 mAh', $content);
    }

    public function test_theme_editor_saves_about_content_with_table(): void
    {
        $theme = Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);

        $this->actingAdmin();

        Livewire::test(ThemeEditor::class)
            ->fillForm([
                'about' => [
                    'content' => [
                        'id' => self::TABLE_HTML,
                        'en' => '<p>Hello</p>',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $content = ThemeSetting::get($theme->id, 'about', 'content');

        $this->assertIsArray($content);
        $this->assertStringContainsString('<table', (string) ($content['id'] ?? ''));
        $this->assertStringContainsString('5000 mAh', (string) ($content['id'] ?? ''));
    }
}

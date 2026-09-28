<?php

namespace Tests\Feature;

use App\Domain\Setting\Filament\Pages\ManageSettings;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $this->artisan('migrate', ['--path' => 'database/settings', '--force' => true]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_can_update_general_settings(): void
    {
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin);

        Livewire::test(ManageSettings::class)
            ->fillForm([
                'site_name' => 'Blog Baru',
                'site_description' => 'Deskripsi uji',
                'default_locale' => 'id',
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd M Y',
                'driver' => 'log',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Blog Baru', app(GeneralSettings::class)->site_name);
        $this->assertSame('Blog Baru', setting('site_name'));
    }

    public function test_setting_helper_returns_value(): void
    {
        $general = app(GeneralSettings::class);
        $general->site_name = 'CMS Helper Test';
        $general->save();

        $this->assertSame('CMS Helper Test', setting('site_name'));
        $this->assertSame('fallback', setting('unknown_key', 'fallback'));
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $editor = User::factory()->create(['is_active' => true]);
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get('/kelola/settings')
            ->assertForbidden();
    }
}

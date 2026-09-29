<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaFilamentAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_super_admin_can_access_media_gallery_page(): void
    {
        $superAdmin = User::query()->where('email', 'admin@admin.com')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get('/kelola/media')
            ->assertOk();
    }

    public function test_user_without_media_permission_gets_forbidden_not_not_found(): void
    {
        $client = User::factory()->create(['is_active' => true]);
        $client->assignRole('client');

        $this->actingAs($client)
            ->get('/kelola/media')
            ->assertForbidden();
    }

    public function test_admin_media_url_redirects_to_kelola_media(): void
    {
        $this->get('/admin/media')
            ->assertRedirect('/kelola/media');
    }
}

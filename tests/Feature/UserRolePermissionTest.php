<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole('admin');

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_super_admin_can_access_user_resource(): void
    {
        $superAdmin = User::factory()->create([
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get('/kelola/users')
            ->assertSuccessful();
    }

    public function test_editor_cannot_access_role_resource(): void
    {
        $editor = User::factory()->create([
            'is_active' => true,
        ]);
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get('/kelola/roles')
            ->assertForbidden();
    }
}

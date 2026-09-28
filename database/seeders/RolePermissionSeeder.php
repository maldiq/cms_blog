<?php

namespace Database\Seeders;

use App\Domain\Role\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $permissions = [
        'user.viewAny',
        'user.create',
        'user.update',
        'user.delete',
        'role.viewAny',
        'role.create',
        'role.update',
        'role.delete',
        'setting.viewAny',
        'setting.update',
    ];

    /**
     * @var list<string>
     */
    private array $roles = [
        'super-admin',
        'admin',
        'editor',
        'author',
        'contributor',
        'client',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach ($this->roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        Role::findByName('super-admin', 'web')->syncPermissions($this->permissions);

        Role::findByName('admin', 'web')->syncPermissions([
            'user.viewAny',
            'user.create',
            'user.update',
            'user.delete',
            'role.viewAny',
            'setting.viewAny',
            'setting.update',
        ]);

        Role::findByName('editor', 'web')->syncPermissions([
            'user.viewAny',
            'user.update',
            'setting.viewAny',
        ]);

        Role::findByName('author', 'web')->syncPermissions([
            'user.viewAny',
            'setting.viewAny',
        ]);

        Role::findByName('contributor', 'web')->syncPermissions([
            'user.viewAny',
        ]);

        Role::findByName('client', 'web')->syncPermissions([
            'setting.viewAny',
        ]);
    }
}

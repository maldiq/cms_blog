<?php

use App\Domain\Role\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'media.viewAny',
            'media.upload',
            'media.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::query()->where('name', 'super-admin')->where('guard_name', 'web')->first()
            ?->givePermissionTo($permissions);

        Role::query()->where('name', 'admin')->where('guard_name', 'web')->first()
            ?->givePermissionTo($permissions);

        Role::query()->where('name', 'editor')->where('guard_name', 'web')->first()
            ?->givePermissionTo([
                'media.viewAny',
                'media.upload',
            ]);
    }

    public function down(): void
    {
        // Permission tetap di DB agar tidak merusak role lain.
    }
};

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
        'media.viewAny',
        'media.upload',
        'media.delete',
        'menu.viewAny',
        'menu.create',
        'menu.update',
        'menu.delete',
        'blog.post.viewAny',
        'blog.post.create',
        'blog.post.update',
        'blog.post.delete',
        'blog.category.viewAny',
        'blog.category.create',
        'blog.category.update',
        'blog.category.delete',
        'blog.tag.viewAny',
        'blog.tag.create',
        'blog.tag.update',
        'blog.tag.delete',
        'blog.series.viewAny',
        'blog.series.create',
        'blog.series.update',
        'blog.series.delete',
        'gallery.album.viewAny',
        'gallery.album.create',
        'gallery.album.update',
        'gallery.album.delete',
        'comment.viewAny',
        'comment.update',
        'comment.delete',
        'page.viewAny',
        'page.create',
        'page.update',
        'page.delete',
        'service.service.viewAny',
        'service.service.create',
        'service.service.update',
        'service.service.delete',
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
            'media.viewAny',
            'media.upload',
            'media.delete',
            'menu.viewAny',
            'menu.create',
            'menu.update',
            'menu.delete',
            'blog.post.viewAny',
            'blog.post.create',
            'blog.post.update',
            'blog.post.delete',
            'blog.category.viewAny',
            'blog.category.create',
            'blog.category.update',
            'blog.tag.viewAny',
            'blog.tag.create',
            'blog.tag.update',
            'blog.series.viewAny',
            'blog.series.create',
            'blog.series.update',
            'gallery.album.viewAny',
            'gallery.album.create',
            'gallery.album.update',
            'gallery.album.delete',
            'comment.viewAny',
            'comment.update',
            'comment.delete',
            'page.viewAny',
            'page.create',
            'page.update',
            'page.delete',
            'service.service.viewAny',
            'service.service.create',
            'service.service.update',
            'service.service.delete',
        ]);

        Role::findByName('editor', 'web')->syncPermissions([
            'user.viewAny',
            'user.update',
            'setting.viewAny',
            'media.viewAny',
            'media.upload',
            'blog.post.viewAny',
            'blog.post.create',
            'blog.post.update',
            'blog.category.viewAny',
            'blog.tag.viewAny',
            'gallery.album.viewAny',
            'gallery.album.create',
            'gallery.album.update',
            'comment.viewAny',
            'comment.update',
            'page.viewAny',
            'page.create',
            'page.update',
            'service.service.viewAny',
            'service.service.create',
            'service.service.update',
        ]);

        Role::findByName('author', 'web')->syncPermissions([
            'user.viewAny',
            'setting.viewAny',
            'blog.post.viewAny',
            'blog.post.create',
            'blog.post.update',
        ]);

        Role::findByName('contributor', 'web')->syncPermissions([
            'user.viewAny',
        ]);

        Role::findByName('client', 'web')->syncPermissions([
            'setting.viewAny',
        ]);
    }
}

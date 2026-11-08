<?php

namespace App\Domain\Role\Support;

use Spatie\Permission\Models\Permission;

class PermissionGroups
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        $groups = [];

        foreach (Permission::query()->orderBy('name')->get(['id', 'name']) as $permission) {
            $segments = explode('.', $permission->name);
            $group = count($segments) > 2
                ? implode('.', array_slice($segments, 0, -1))
                : $segments[0];

            $groups[$group][$permission->id] = $permission->name;
        }

        return $groups;
    }

    public static function fieldKey(string $group): string
    {
        return 'permissions_'.str_replace('.', '_', $group);
    }

    public static function label(string $group): string
    {
        return str($group)->replace('.', ' / ')->headline()->toString();
    }
}

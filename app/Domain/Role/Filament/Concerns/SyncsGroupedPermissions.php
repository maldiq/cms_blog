<?php

namespace App\Domain\Role\Filament\Concerns;

use Spatie\Permission\Models\Permission;

trait SyncsGroupedPermissions
{
    /**
     * @var list<int>
     */
    protected array $permissionIdsToSync = [];

    /**
     * @param  array<string, mixed>  $data
     * @return list<int>
     */
    protected function collectPermissionIds(array $data): array
    {
        $ids = [];

        foreach (['user', 'role', 'setting'] as $prefix) {
            $key = "permissions_{$prefix}";
            if (! empty($data[$key]) && is_array($data[$key])) {
                $ids = array_merge($ids, $data[$key]);
            }
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * @return array<string, mixed>
     */
    protected function stripPermissionFields(array $data): array
    {
        unset($data['permissions_user'], $data['permissions_role'], $data['permissions_setting']);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function fillGroupedPermissionFields(array $data): array
    {
        $permissionIds = $this->record->permissions()->pluck('id');

        foreach (['user', 'role', 'setting'] as $prefix) {
            $data["permissions_{$prefix}"] = Permission::query()
                ->where('name', 'like', "{$prefix}.%")
                ->whereIn('id', $permissionIds)
                ->pluck('id')
                ->all();
        }

        return $data;
    }
}

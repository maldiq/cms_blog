<?php

namespace App\Domain\Role\Filament\Concerns;

use App\Domain\Role\Support\PermissionGroups;
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

        foreach (PermissionGroups::all() as $group => $permissions) {
            $key = PermissionGroups::fieldKey($group);
            if (! empty($data[$key]) && is_array($data[$key])) {
                $ids = array_merge($ids, array_intersect(
                    array_map('intval', $data[$key]),
                    array_map('intval', array_keys($permissions)),
                ));
            }
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * @return array<string, mixed>
     */
    protected function stripPermissionFields(array $data): array
    {
        foreach (PermissionGroups::all() as $group => $_permissions) {
            unset($data[PermissionGroups::fieldKey($group)]);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function fillGroupedPermissionFields(array $data): array
    {
        $permissionIds = $this->record->permissions()->pluck('id');

        foreach (PermissionGroups::all() as $group => $permissions) {
            $data[PermissionGroups::fieldKey($group)] = Permission::query()
                ->whereIn('name', array_values($permissions))
                ->whereIn('id', $permissionIds)
                ->pluck('id')
                ->all();
        }

        return $data;
    }
}

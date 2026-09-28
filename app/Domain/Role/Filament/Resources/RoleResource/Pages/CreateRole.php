<?php

namespace App\Domain\Role\Filament\Resources\RoleResource\Pages;

use App\Domain\Role\Filament\Concerns\SyncsGroupedPermissions;
use App\Domain\Role\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    use SyncsGroupedPermissions;

    protected static string $resource = RoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionIdsToSync = $this->collectPermissionIds($data);

        return $this->stripPermissionFields($data);
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permissionIdsToSync);
    }
}

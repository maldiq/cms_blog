<?php

namespace App\Domain\Role\Filament\Resources\RoleResource\Pages;

use App\Domain\Role\Filament\Concerns\SyncsGroupedPermissions;
use App\Domain\Role\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    use SyncsGroupedPermissions;

    protected static string $resource = RoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillGroupedPermissionFields($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissionIdsToSync = $this->collectPermissionIds($data);

        return $this->stripPermissionFields($data);
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissions($this->permissionIdsToSync);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

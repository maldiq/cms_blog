<?php

namespace App\Domain\Language\Filament\Resources\LanguageResource\Pages;

use App\Domain\Language\Filament\Resources\LanguageResource;
use App\Domain\Language\Models\Language;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['is_default'])) {
            Language::query()->where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            DB::transaction(function (): void {
                Language::query()
                    ->where('id', '!=', $this->record->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            });
        }
    }
}

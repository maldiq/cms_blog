<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliatePlatformTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliatePlatform extends EditRecord
{
    use SyncsAffiliatePlatformTranslations;

    protected static string $resource = AffiliatePlatformResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var AffiliatePlatform $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only(['name']);
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncAffiliatePlatformTranslations($this->record, $this->form->getState());
    }
}

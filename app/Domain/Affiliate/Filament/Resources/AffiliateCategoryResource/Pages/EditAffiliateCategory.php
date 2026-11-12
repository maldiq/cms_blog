<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateCategoryTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateCategory extends EditRecord
{
    use SyncsAffiliateCategoryTranslations;

    protected static string $resource = AffiliateCategoryResource::class;

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
        /** @var AffiliateCategory $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only(['name', 'slug']);
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
        $this->syncAffiliateCategoryTranslations($this->record, $this->form->getState());
    }
}

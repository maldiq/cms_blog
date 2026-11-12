<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateProductTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateProduct extends EditRecord
{
    use InteractsWithAffiliateProductImage;
    use SyncsAffiliateProductTranslations;

    protected static string $resource = AffiliateProductResource::class;

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
        /** @var AffiliateProduct $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'description',
                ]);
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
        $this->syncAffiliateProductTranslations($this->record, $this->form->getState());
    }
}

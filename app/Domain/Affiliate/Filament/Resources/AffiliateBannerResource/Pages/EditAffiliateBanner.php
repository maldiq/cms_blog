<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages;

use App\Domain\Affiliate\Filament\Concerns\SyncsAffiliateBannerTranslations;
use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns\InteractsWithAffiliateProductImage;
use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateBanner extends EditRecord
{
    use InteractsWithAffiliateProductImage;
    use SyncsAffiliateBannerTranslations;

    protected static string $resource = AffiliateBannerResource::class;

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
        /** @var AffiliateBanner $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'badge', 'title', 'subtitle', 'cta_label',
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
        $this->syncAffiliateBannerTranslations($this->record, $this->form->getState());
    }
}

<?php

namespace App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages;

use App\Domain\Language\Models\Language;
use App\Domain\Portfolio\Filament\Concerns\SyncsPortfolioTranslations;
use App\Domain\Portfolio\Filament\Resources\PortfolioResource;
use App\Domain\Portfolio\Models\Portfolio;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPortfolio extends EditRecord
{
    use SyncsPortfolioTranslations;

    protected static string $resource = PortfolioResource::class;

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
        /** @var Portfolio $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'excerpt', 'content',
                    'meta_title', 'meta_description', 'meta_keywords',
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
        $this->syncPortfolioTranslations($this->record, $this->form->getState());
    }
}

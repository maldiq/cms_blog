<?php

namespace App\Domain\Service\Filament\Resources\ServiceResource\Pages;

use App\Domain\Language\Models\Language;
use App\Domain\Service\Filament\Concerns\SyncsServiceTranslations;
use App\Domain\Service\Filament\Resources\ServiceResource;
use App\Domain\Service\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use SyncsServiceTranslations;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Service $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
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
        $this->syncServiceTranslations($this->record, $this->form->getState());
    }
}

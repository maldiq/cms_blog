<?php

namespace App\Domain\Page\Filament\Resources\PageResource\Pages;

use App\Domain\Language\Models\Language;
use App\Domain\Page\Filament\Concerns\SyncsPageTranslations;
use App\Domain\Page\Filament\Resources\PageResource;
use App\Domain\Page\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use SyncsPageTranslations;

    protected static string $resource = PageResource::class;

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
        /** @var Page $record */
        $record = $this->record;

        foreach (Language::getActive() as $language) {
            $translation = $record->translate($language->code, false);

            if ($translation !== null) {
                $data['translations'][$language->code] = $translation->only([
                    'title', 'slug', 'content', 'meta_title', 'meta_description',
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
        $this->syncPageTranslations($this->record, $this->form->getState());
    }
}

<?php

namespace App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages;

use App\Domain\Portfolio\Filament\Concerns\SyncsPortfolioTranslations;
use App\Domain\Portfolio\Filament\Resources\PortfolioResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortfolio extends CreateRecord
{
    use SyncsPortfolioTranslations;

    protected static string $resource = PortfolioResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translations']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncPortfolioTranslations($this->record, $this->form->getState());
    }
}

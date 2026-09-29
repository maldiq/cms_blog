<?php

namespace App\Domain\Comment\Filament\Resources\CommentResource\Pages;

use App\Domain\Comment\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->content !== ($data['content'] ?? null)) {
            $data['edited_at'] = now();
        }

        return $data;
    }
}

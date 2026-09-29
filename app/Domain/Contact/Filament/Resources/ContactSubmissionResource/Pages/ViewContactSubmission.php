<?php

namespace App\Domain\Contact\Filament\Resources\ContactSubmissionResource\Pages;

use App\Domain\Contact\Filament\Resources\ContactSubmissionResource;
use App\Domain\Contact\Models\ContactSubmission;
use App\Domain\Contact\Services\ContactSubmissionService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markRead')
                ->label('Tandai dibaca')
                ->icon('heroicon-o-eye')
                ->visible(fn (): bool => $this->getRecord()->status === ContactSubmission::STATUS_NEW)
                ->action(function (): void {
                    app(ContactSubmissionService::class)->markStatus($this->getRecord(), ContactSubmission::STATUS_READ);
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('markReplied')
                ->label('Tandai dibalas')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->action(function (): void {
                    app(ContactSubmissionService::class)->markStatus($this->getRecord(), ContactSubmission::STATUS_REPLIED);
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('archive')
                ->label('Arsipkan')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->action(function (): void {
                    app(ContactSubmissionService::class)->markStatus($this->getRecord(), ContactSubmission::STATUS_ARCHIVED);
                    $this->refreshFormData(['status']);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}

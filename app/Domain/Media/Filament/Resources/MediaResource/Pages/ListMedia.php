<?php

namespace App\Domain\Media\Filament\Resources\MediaResource\Pages;

use App\Domain\Media\Filament\Resources\MediaResource;
use App\Domain\Media\Services\MediaUploadService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload')
                ->label('Upload media')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn (): bool => auth()->user()?->can('media.upload') ?? false)
                ->form([
                    Forms\Components\FileUpload::make('files')
                        ->label('File')
                        ->multiple()
                        ->disk('public')
                        ->directory('media-uploads')
                        ->visibility('public')
                        ->acceptedFileTypes([
                            'image/*',
                            'application/pdf',
                            'video/mp4',
                        ])
                        ->maxSize(10240)
                        ->required(),
                ])
                ->action(function (array $data, MediaUploadService $mediaUploadService): void {
                    $paths = (array) ($data['files'] ?? []);
                    $count = count($paths);

                    $mediaUploadService->uploadManyFromPublicPaths($paths);

                    Notification::make()
                        ->title("{$count} file berhasil diunggah")
                        ->success()
                        ->send();
                }),
        ];
    }
}

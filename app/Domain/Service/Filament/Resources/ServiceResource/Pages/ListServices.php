<?php

namespace App\Domain\Service\Filament\Resources\ServiceResource\Pages;

use App\Domain\Service\Filament\Resources\ServiceResource;
use App\Domain\Service\Services\SolusiwebServiceImportService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importSolusiweb')
                ->label('Impor dari Solusiweb')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Impor layanan dari Solusiweb')
                ->modalDescription('Semua layanan lokal akan dihapus lalu diisi ulang dari solusiweb.test (daftar slug tetap di kode). Pastikan situs sumber dapat diakses.')
                ->action(function (SolusiwebServiceImportService $importService): void {
                    try {
                        $result = $importService->import();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Impor gagal')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Impor selesai')
                        ->body("{$result['imported']} layanan dari {$result['source']}. Menu header disinkronkan.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}

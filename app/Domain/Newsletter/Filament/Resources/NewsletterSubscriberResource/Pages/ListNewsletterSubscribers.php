<?php

namespace App\Domain\Newsletter\Filament\Resources\NewsletterSubscriberResource\Pages;

use App\Domain\Newsletter\Filament\Resources\NewsletterSubscriberResource;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\Newsletter\Services\NewsletterSubscriberService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListNewsletterSubscribers extends ListRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportAllCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): mixed {
                    Gate::authorize('export', NewsletterSubscriber::class);

                    $query = $this->getFilteredTableQuery();

                    return app(NewsletterSubscriberService::class)->exportCsv(
                        $query->get(),
                        'newsletter-subscribers-'.now()->format('Y-m-d-His').'.csv',
                    );
                }),
        ];
    }
}

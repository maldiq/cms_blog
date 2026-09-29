<?php

namespace App\Domain\Newsletter\Filament\Resources;

use App\Domain\Language\Models\Language;
use App\Domain\Newsletter\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\Newsletter\Services\NewsletterSubscriberService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'Subscriber Newsletter';

    protected static ?string $pluralModelLabel = 'Subscriber Newsletter';

    protected static ?string $slug = 'newsletter-subscribers';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('email')->label('Email')->disabled(),
            Forms\Components\TextInput::make('locale')->label('Locale')->disabled(),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->disabled(),
            Forms\Components\DateTimePicker::make('subscribed_at')->label('Berlangganan')->disabled(),
            Forms\Components\DateTimePicker::make('unsubscribed_at')->label('Berhenti')->disabled(),
            Forms\Components\TextInput::make('ip_address')->label('IP')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('locale')->label('Locale')->badge()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('subscribed_at')->label('Berlangganan')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('unsubscribed_at')->label('Berhenti')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')->label('Aktif'),
                SelectFilter::make('locale')
                    ->label('Locale')
                    ->options(fn (): array => Language::getActive()->pluck('code', 'code')->all()),
                Filter::make('subscribed_at')
                    ->label('Tanggal berlangganan')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('subscribed_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('subscribed_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('unsubscribe')
                    ->label('Unsubscribe')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (NewsletterSubscriber $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (NewsletterSubscriber $record): void {
                        app(NewsletterSubscriberService::class)->unsubscribe($record);
                    }),
                Tables\Actions\Action::make('exportCsv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (NewsletterSubscriber $record) {
                        Gate::authorize('export', NewsletterSubscriber::class);

                        return app(NewsletterSubscriberService::class)->exportCsv(
                            collect([$record]),
                            'newsletter-'.$record->id.'.csv',
                        );
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('unsubscribe')
                        ->label('Unsubscribe')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(
                            fn (NewsletterSubscriber $record) => app(NewsletterSubscriberService::class)->unsubscribe($record),
                        )),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            Gate::authorize('export', NewsletterSubscriber::class);

                            return app(NewsletterSubscriberService::class)->exportCsv(
                                $records,
                                'newsletter-subscribers-'.now()->format('Y-m-d-His').'.csv',
                            );
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
        ];
    }
}

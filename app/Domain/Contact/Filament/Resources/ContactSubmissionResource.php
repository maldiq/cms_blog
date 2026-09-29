<?php

namespace App\Domain\Contact\Filament\Resources;

use App\Domain\Contact\Filament\Resources\ContactSubmissionResource\Pages;
use App\Domain\Contact\Models\ContactSubmission;
use App\Domain\Contact\Services\ContactSubmissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 9;

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?string $pluralModelLabel = 'Pesan Kontak';

    protected static ?string $slug = 'contact-submissions';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Pengirim')->schema([
                Infolists\Components\TextEntry::make('name')->label('Nama'),
                Infolists\Components\TextEntry::make('email')->label('Email'),
                Infolists\Components\TextEntry::make('phone')->label('Telepon')->placeholder('-'),
                Infolists\Components\TextEntry::make('locale')->label('Locale')->badge(),
            ])->columns(2),
            Infolists\Components\Section::make('Pesan')->schema([
                Infolists\Components\TextEntry::make('subject')->label('Subjek'),
                Infolists\Components\TextEntry::make('message')->label('Isi')->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Meta')->schema([
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactSubmission::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactSubmission::STATUS_NEW => 'warning',
                        ContactSubmission::STATUS_READ => 'info',
                        ContactSubmission::STATUS_REPLIED => 'success',
                        ContactSubmission::STATUS_ARCHIVED => 'gray',
                        default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('ip_address')->label('IP'),
                Infolists\Components\TextEntry::make('user_agent')->label('User agent')->columnSpanFull(),
                Infolists\Components\TextEntry::make('created_at')->label('Diterima')->dateTime(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('subject')->label('Subjek')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactSubmission::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ContactSubmission::STATUS_NEW => 'warning',
                        ContactSubmission::STATUS_READ => 'info',
                        ContactSubmission::STATUS_REPLIED => 'success',
                        ContactSubmission::STATUS_ARCHIVED => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Diterima')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ContactSubmission::statusLabels()),
                Filter::make('created_at')
                    ->label('Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('markRead')
                    ->label('Dibaca')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (ContactSubmission $record): bool => $record->status === ContactSubmission::STATUS_NEW)
                    ->action(fn (ContactSubmission $record) => app(ContactSubmissionService::class)->markStatus($record, ContactSubmission::STATUS_READ)),
                Tables\Actions\Action::make('markReplied')
                    ->label('Dibalas')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->action(fn (ContactSubmission $record) => app(ContactSubmissionService::class)->markStatus($record, ContactSubmission::STATUS_REPLIED)),
                Tables\Actions\Action::make('archive')
                    ->label('Arsip')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn (ContactSubmission $record) => app(ContactSubmissionService::class)->markStatus($record, ContactSubmission::STATUS_ARCHIVED)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markRead')
                        ->label('Tandai dibaca')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each(
                            fn (ContactSubmission $record) => app(ContactSubmissionService::class)->markStatus($record, ContactSubmission::STATUS_READ),
                        )),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('Arsipkan')
                        ->icon('heroicon-o-archive-box')
                        ->action(fn ($records) => $records->each(
                            fn (ContactSubmission $record) => app(ContactSubmissionService::class)->markStatus($record, ContactSubmission::STATUS_ARCHIVED),
                        )),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSubmissions::route('/'),
            'view' => Pages\ViewContactSubmission::route('/{record}'),
        ];
    }
}

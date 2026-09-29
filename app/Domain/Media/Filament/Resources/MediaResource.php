<?php

namespace App\Domain\Media\Filament\Resources;

use App\Domain\Media\Filament\Resources\MediaResource\Pages;
use App\Domain\Media\Models\Media;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Media';

    protected static ?string $modelLabel = 'Media';

    protected static ?string $pluralModelLabel = 'Media';

    protected static ?string $slug = 'media';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('preview')
                        ->label('')
                        ->height('10rem')
                        ->width('100%')
                        ->getStateUsing(function (Media $record): ?string {
                            if (! is_string($record->mime_type) || ! str_starts_with($record->mime_type, 'image/')) {
                                return null;
                            }

                            if ($record->hasGeneratedConversion('thumb')) {
                                return $record->getFullUrl('thumb');
                            }

                            return $record->getFullUrl();
                        }),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Nama')
                        ->searchable()
                        ->weight('bold')
                        ->limit(30),
                    Tables\Columns\TextColumn::make('collection_name')
                        ->label('Koleksi')
                        ->badge(),
                    Tables\Columns\TextColumn::make('mime_type')
                        ->label('MIME')
                        ->size('sm')
                        ->color('gray'),
                    Tables\Columns\TextColumn::make('human_readable_size')
                        ->label('Ukuran')
                        ->size('sm')
                        ->color('gray'),
                ])->space(2),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('collection_name')
                    ->label('Koleksi')
                    ->options(fn (): array => Media::query()
                        ->select('collection_name')
                        ->distinct()
                        ->orderBy('collection_name')
                        ->pluck('collection_name', 'collection_name')
                        ->all()),
                Tables\Filters\SelectFilter::make('mime_type')
                    ->label('Tipe MIME')
                    ->options(fn (): array => Media::query()
                        ->whereNotNull('mime_type')
                        ->select('mime_type')
                        ->distinct()
                        ->orderBy('mime_type')
                        ->pluck('mime_type', 'mime_type')
                        ->all()),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Media $record): string => $record->getFullUrl())
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('copyUrl')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard-document')
                    ->action(function (Media $record): void {
                        Notification::make()
                            ->title('URL media')
                            ->body($record->getFullUrl())
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(fn (Media $record): array => [
                        'x-on:click' => 'window.navigator.clipboard.writeText('.json_encode($record->getFullUrl()).')',
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([12, 24, 48]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest('created_at');
    }
}

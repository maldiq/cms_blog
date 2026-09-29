<?php

namespace App\Domain\Gallery\Album\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Gallery\Album\Filament\Resources\AlbumResource\Pages;
use App\Domain\Gallery\Album\Filament\Resources\AlbumResource\RelationManagers\MediaRelationManager;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Media\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlbumResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Album::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Galeri';

    protected static ?string $modelLabel = 'Album';

    protected static ?string $pluralModelLabel = 'Album';

    protected static ?string $slug = 'albums';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Judul')
                    ->required($locale === 'id')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Set $set, ?string $state) use ($locale): void {
                        $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state));
                    }),
                Forms\Components\TextInput::make("translations.{$locale}.slug")
                    ->label('Slug')
                    ->maxLength(255),
                Forms\Components\Textarea::make("translations.{$locale}.description")
                    ->label('Deskripsi')
                    ->rows(3),
            ]),
            Forms\Components\Section::make('Pengaturan')
                ->schema([
                    Forms\Components\Select::make('cover_id')
                        ->label('Cover')
                        ->searchable()
                        ->options(fn (): array => Media::query()->latest()->limit(100)->pluck('name', 'id')->all())
                        ->nullable(),
                    Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                    Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Cover')
                    ->getStateUsing(fn (Album $record): ?string => $record->cover?->getUrl('thumb')),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Album $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('media_count')->counts('media')->label('Foto'),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}

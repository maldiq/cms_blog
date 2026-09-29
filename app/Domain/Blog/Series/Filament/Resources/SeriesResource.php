<?php

namespace App\Domain\Blog\Series\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages;
use App\Domain\Blog\Series\Filament\Resources\SeriesResource\RelationManagers\PostsRelationManager;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Media\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Series';

    protected static ?string $slug = 'series';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Judul')
                    ->required($locale === 'id')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")->label('Slug'),
                Forms\Components\Textarea::make("translations.{$locale}.description")->label('Deskripsi')->rows(3),
                Forms\Components\TextInput::make("translations.{$locale}.meta_title")->label('Meta title'),
                Forms\Components\TextInput::make("translations.{$locale}.meta_description")->label('Meta description'),
            ]),
            Forms\Components\Section::make('Pengaturan')
                ->schema([
                    Forms\Components\Select::make('cover_id')
                        ->label('Cover')
                        ->searchable()
                        ->options(fn (): array => Media::query()->latest()->limit(100)->pluck('name', 'id')->all()),
                    Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Series $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Post'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getRelations(): array
    {
        return [PostsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}

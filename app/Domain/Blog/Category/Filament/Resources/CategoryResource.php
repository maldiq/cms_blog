<?php

namespace App\Domain\Blog\Category\Filament\Resources;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource\Pages;
use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Media\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $slug = 'categories';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.name")
                    ->label('Nama')
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
                Forms\Components\TextInput::make("translations.{$locale}.meta_title")
                    ->label('Meta title')
                    ->maxLength(255),
                Forms\Components\TextInput::make("translations.{$locale}.meta_description")
                    ->label('Meta description')
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->getStateUsing(fn (Category $record): string => (string) $record->translate(app()->getLocale(), false)?->name),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

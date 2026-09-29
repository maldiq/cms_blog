<?php

namespace App\Domain\Service\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Service\Filament\Resources\ServiceResource\Pages;
use App\Domain\Service\Models\Service;
use App\Domain\Theme\Filament\Forms\Components\MediaPickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?string $slug = 'services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Judul')
                    ->required($locale === 'id')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")
                    ->label('Slug')
                    ->maxLength(255),
                Forms\Components\Textarea::make("translations.{$locale}.excerpt")
                    ->label('Excerpt')
                    ->rows(3),
                Forms\Components\RichEditor::make("translations.{$locale}.content")
                    ->label('Konten')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make("translations.{$locale}.meta_title")->label('Meta title'),
                Forms\Components\TextInput::make("translations.{$locale}.meta_description")->label('Meta description'),
                Forms\Components\TextInput::make("translations.{$locale}.meta_keywords")->label('Meta keywords'),
            ]),
            Forms\Components\Section::make('Pengaturan')
                ->schema([
                    Forms\Components\TextInput::make('icon')->label('Icon')->maxLength(100),
                    MediaPickerField::make('cover_id')->label('Cover'),
                    Forms\Components\TextInput::make('price_from')->label('Harga dari')->numeric(),
                    Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                    Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
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
                    ->getStateUsing(fn (Service $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

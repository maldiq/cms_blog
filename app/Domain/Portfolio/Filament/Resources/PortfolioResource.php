<?php

namespace App\Domain\Portfolio\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Portfolio\Filament\Resources\PortfolioResource\Pages;
use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Portfolio\Models\PortfolioCategory;
use App\Domain\Theme\Filament\Forms\Components\MediaPickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PortfolioResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Portfolio';

    protected static ?string $slug = 'portfolios';

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
                Forms\Components\Textarea::make("translations.{$locale}.excerpt")->label('Excerpt')->rows(3),
                Forms\Components\RichEditor::make("translations.{$locale}.content")->label('Konten')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Pengaturan')->schema([
                MediaPickerField::make('cover_id')->label('Cover'),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(fn (): array => PortfolioCategory::query()
                        ->with('translations')
                        ->get()
                        ->mapWithKeys(fn (PortfolioCategory $category): array => [
                            $category->id => (string) $category->translate(app()->getLocale(), false)?->name,
                        ])
                        ->all())
                    ->searchable(),
                Forms\Components\TextInput::make('client_name')->label('Klien'),
                Forms\Components\DatePicker::make('project_date')->label('Tanggal proyek'),
                Forms\Components\TextInput::make('project_url')->label('URL proyek')->url(),
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Portfolio $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}

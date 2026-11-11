<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateCategoryResource\Pages;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AffiliateCategoryResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = AffiliateCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Kategori Affiliate';

    protected static ?string $pluralModelLabel = 'Kategori Affiliate';

    protected static ?string $slug = 'affiliate-categories';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.name")
                    ->label('Nama kategori')
                    ->required($locale === 'id')
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")
                    ->label('Slug')
                    ->maxLength(120)
                    ->helperText('Dipakai pada URL kategori. Kosongkan untuk otomatis dari nama.'),
            ]),
            Forms\Components\Section::make('Pengaturan')->schema([
                Forms\Components\TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori')
                    ->getStateUsing(fn (AffiliateCategory $record): string => (string) (
                        $record->translate(app()->getLocale(), false)?->name
                        ?? $record->translate('id', false)?->name
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('products_count')->label('Produk')->counts('products')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (AffiliateCategory $record): bool => $record->products()->doesntExist())
                    ->modalDescription('Kategori yang masih digunakan produk tidak dapat dihapus.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateCategories::route('/'),
            'create' => Pages\CreateAffiliateCategory::route('/create'),
            'edit' => Pages\EditAffiliateCategory::route('/{record}/edit'),
        ];
    }
}

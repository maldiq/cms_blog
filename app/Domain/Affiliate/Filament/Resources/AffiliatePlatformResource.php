<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliatePlatformResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = AffiliatePlatform::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'Platform Affiliate';

    protected static ?string $pluralModelLabel = 'Platform Affiliate';

    protected static ?string $slug = 'affiliate-platforms';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.name")
                    ->label('Nama platform')
                    ->required($locale === 'id')
                    ->maxLength(100),
            ]),
            Forms\Components\Section::make('Pengaturan')->schema([
                Forms\Components\TextInput::make('slug')
                    ->label('Kode platform')
                    ->required()
                    ->maxLength(40)
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->dehydrated()
                    ->helperText('Kode internal (mis. shopee, amazon) dan tidak berubah antar bahasa.'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Platform')
                    ->getStateUsing(fn (AffiliatePlatform $record): string => (string) (
                        $record->translate(app()->getLocale(), false)?->name
                        ?? $record->translate('id', false)?->name
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('slug')->label('Kode')->toggleable(),
                Tables\Columns\TextColumn::make('products_count')->label('Produk')->counts('products')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (AffiliatePlatform $record): bool => $record->products()->doesntExist())
                    ->modalDescription('Platform yang masih digunakan produk tidak dapat dihapus.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliatePlatforms::route('/'),
            'create' => Pages\CreateAffiliatePlatform::route('/create'),
            'edit' => Pages\EditAffiliatePlatform::route('/{record}/edit'),
        ];
    }
}

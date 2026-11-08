<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliatePlatformResource\Pages;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AffiliatePlatformResource extends Resource
{
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
            Forms\Components\TextInput::make('name')
                ->label('Nama platform')
                ->required()
                ->maxLength(100)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')
                ->label('Kode platform')
                ->required()
                ->maxLength(40)
                ->unique(ignoreRecord: true)
                ->disabledOn('edit')
                ->dehydrated()
                ->helperText('Kode digunakan sebagai identitas platform dan tidak dapat diubah setelah dibuat.'),
            Forms\Components\TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Platform')->searchable()->sortable(),
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

<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Theme\Filament\Forms\Components\MediaImagePickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AffiliateProductResource extends Resource
{
    protected static ?string $model = AffiliateProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Produk Affiliate';

    protected static ?string $pluralModelLabel = 'Produk Affiliate';

    protected static ?string $slug = 'affiliate-products';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi produk')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nama produk')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\Select::make('affiliate_category_id')
                    ->label('Kategori')
                    ->options(fn (): array => AffiliateCategory::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('affiliate_platform_id')
                    ->label('Platform')
                    ->options(fn (): array => AffiliatePlatform::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(4)->columnSpanFull(),
                MediaImagePickerField::make('image_id')->label('Gambar produk')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Harga dan tautan affiliate')->schema([
                Forms\Components\TextInput::make('price')->label('Harga saat ini')->numeric()->minValue(0)->prefix('Rp')->nullable(),
                Forms\Components\TextInput::make('original_price')->label('Harga sebelum diskon')->numeric()->minValue(0)->prefix('Rp')->nullable(),
                Forms\Components\TextInput::make('affiliate_url')
                    ->label('Tautan affiliate')
                    ->url()
                    ->rules(['url:http,https'])
                    ->required()
                    ->maxLength(2048)
                    ->helperText('Gunakan URL produk atau kampanye affiliate Anda. Produk contoh dari seeder nonaktif sampai Anda mengaktifkannya.')
                    ->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Publikasi')->schema([
                Forms\Components\Toggle::make('is_featured')->label('Rekomendasi unggulan'),
                Forms\Components\Toggle::make('is_active')->label('Tampilkan di website')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Produk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('affiliatePlatform.name')->label('Platform')->badge(),
                Tables\Columns\TextColumn::make('affiliateCategory.name')->label('Kategori')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->money('IDR'),
                Tables\Columns\TextColumn::make('click_count')->label('Klik')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateProducts::route('/'),
            'create' => Pages\CreateAffiliateProduct::route('/create'),
            'edit' => Pages\EditAffiliateProduct::route('/{record}/edit'),
        ];
    }
}

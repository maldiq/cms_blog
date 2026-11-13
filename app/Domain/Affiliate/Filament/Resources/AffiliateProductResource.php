<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Media\Filament\Forms\Components\RichContentEditor;
use App\Domain\Theme\Filament\Forms\Components\MediaImagePickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AffiliateProductResource extends Resource
{
    use InteractsWithLocaleTabs;

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
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Nama produk')
                    ->required($locale === 'id')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")
                    ->label('Slug')
                    ->maxLength(255)
                    ->helperText('Kosongkan untuk dibuat otomatis dari nama produk.'),
                Forms\Components\Textarea::make("translations.{$locale}.excerpt")
                    ->label('Ringkasan singkat')
                    ->helperText('Tampil di atas tombol beli. Cukup 1–2 kalimat.')
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),
                RichContentEditor::make("translations.{$locale}.description")
                    ->label('Deskripsi')
                    ->helperText('Tampil di bagian "Deskripsi produk" di bawah halaman.')
                    ->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Informasi produk')->schema([
                Forms\Components\Select::make('affiliate_category_id')
                    ->label('Kategori')
                    ->options(fn (): array => AffiliateCategory::query()
                        ->where('is_active', true)
                        ->with(['translations' => fn ($query) => $query->where('locale', app()->getLocale())])
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get()
                        ->mapWithKeys(fn (AffiliateCategory $category): array => [
                            $category->getKey() => (string) (
                                $category->translate(app()->getLocale(), false)?->name
                                ?? $category->translate('id', false)?->name
                            ),
                        ])
                        ->all())
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('affiliate_platform_id')
                    ->label('Platform')
                    ->options(fn (): array => AffiliatePlatform::query()
                        ->where('is_active', true)
                        ->with(['translations' => fn ($query) => $query->where('locale', app()->getLocale())])
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get()
                        ->mapWithKeys(fn (AffiliatePlatform $platform): array => [
                            $platform->getKey() => (string) (
                                $platform->translate(app()->getLocale(), false)?->name
                                ?? $platform->translate('id', false)?->name
                            ),
                        ])
                        ->all())
                    ->searchable()
                    ->required(),
                MediaImagePickerField::make('image_id')
                    ->label('Gambar produk')
                    ->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Harga dan tautan affiliate')->schema([
                Forms\Components\TextInput::make('price')
                    ->label('Harga saat ini')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->helperText('Harga disimpan dalam IDR. Tampilan situs dikonversi sesuai mata uang bahasa.')
                    ->nullable(),
                Forms\Components\TextInput::make('original_price')
                    ->label('Harga sebelum diskon')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->helperText('Harga disimpan dalam IDR.')
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Produk')
                    ->getStateUsing(fn (AffiliateProduct $record): string => (string) (
                        $record->translate(app()->getLocale(), false)?->title
                        ?? $record->translate('id', false)?->title
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('affiliatePlatform.name')->label('Platform')->badge(),
                Tables\Columns\TextColumn::make('affiliateCategory.name')->label('Kategori'),
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

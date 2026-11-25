<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages;
use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\RelationManagers\DestinationsRelationManager;
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
                Forms\Components\Select::make('billing_period')
                    ->label('Periode harga')
                    ->options([
                        'monthly' => 'Per bulan',
                        'yearly' => 'Per tahun',
                        'triennial' => 'Per 3 tahun',
                        'one_time' => 'Sekali bayar',
                    ])
                    ->placeholder('Tidak disebutkan')
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('is_hosting'))
                    ->helperText('Dipakai untuk label harga, mis. "Rp65.000 /bulan".'),
                Forms\Components\TextInput::make('affiliate_url')
                    ->label('Tautan affiliate')
                    ->url()
                    ->rules(['url:http,https'])
                    ->required()
                    ->maxLength(2048)
                    ->helperText('Tautan utama. Perubahan di sini ikut memperbarui tujuan utama. Tambahkan tujuan cadangan dari tab "Tujuan Link".')
                    ->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Jenis produk')
                ->schema([
                    Forms\Components\Toggle::make('is_hosting')
                        ->label('Produk hosting / VPS')
                        ->helperText('Aktifkan agar tabel spesifikasi server tampil di halaman produk dan kategori. Produk marketplace biasa biarkan nonaktif.')
                        ->live(),
                ]),
            Forms\Components\Section::make('Spesifikasi VPS / Hosting')
                ->description('Isi untuk produk hosting, VPS, atau cloud. Kolom kosong tidak ditampilkan di situs.')
                ->visible(fn (Forms\Get $get): bool => (bool) $get('is_hosting'))
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('cpu_cores')
                        ->label('CPU (vCore)')
                        ->numeric()
                        ->minValue(1),
                    Forms\Components\TextInput::make('ram_mb')
                        ->label('RAM (MB)')
                        ->numeric()
                        ->minValue(0)
                        ->helperText('Contoh: 1024 untuk 1 GB.'),
                    Forms\Components\TextInput::make('storage_gb')
                        ->label('Penyimpanan (GB)')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\Select::make('storage_type')
                        ->label('Tipe penyimpanan')
                        ->options([
                            'NVMe' => 'NVMe',
                            'SSD' => 'SSD',
                            'HDD' => 'HDD',
                            'SAS' => 'SAS',
                        ]),
                    Forms\Components\TextInput::make('bandwidth_tb')
                        ->label('Bandwidth (TB)')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\TextInput::make('ipv4_count')
                        ->label('Jumlah IPv4')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\Toggle::make('ipv6_support')
                        ->label('Dukungan IPv6'),
                    Forms\Components\TextInput::make('port_speed')
                        ->label('Port')
                        ->maxLength(20)
                        ->placeholder('1Gbps'),
                    Forms\Components\TextInput::make('location')
                        ->label('Lokasi datacenter')
                        ->maxLength(120)
                        ->placeholder('Singapore, Amsterdam, dll.'),
                    Forms\Components\DateTimePicker::make('last_checked_at')
                        ->label('Terakhir dicek')
                        ->seconds(false)
                        ->helperText('Tampil sebagai "Terakhir diperbarui" di halaman kategori.'),
                ])->columns(3),
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
                Tables\Columns\TextColumn::make('affiliate_url')
                    ->label('Tujuan link')
                    ->badge()
                    ->getStateUsing(fn (AffiliateProduct $record): string => $record->usesPlaceholderSearchLink()
                        ? 'Pencarian'
                        : 'Link asli')
                    ->color(fn (string $state): string => $state === 'Pencarian' ? 'warning' : 'success')
                    ->tooltip(fn (AffiliateProduct $record): string => $record->usesPlaceholderSearchLink()
                        ? 'Masih placeholder. Impor link affiliate asli lewat tombol "Impor link".'
                        : (string) $record->affiliate_url),
                Tables\Columns\TextColumn::make('click_count')->label('Klik')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DestinationsRelationManager::class,
        ];
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

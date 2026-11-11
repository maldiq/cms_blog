<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateBannerResource\Pages;
use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Theme\Filament\Forms\Components\MediaImagePickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliateBannerResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = AffiliateBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Banner Slider';

    protected static ?string $pluralModelLabel = 'Banner Slider';

    protected static ?string $slug = 'affiliate-banners';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.badge")
                    ->label('Label kecil')
                    ->maxLength(60)
                    ->placeholder('Contoh: Promo 11.11'),
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Judul slide')
                    ->required($locale === 'id')
                    ->maxLength(150)
                    ->placeholder('Contoh: Belanja pintar, hemat setiap hari.'),
                Forms\Components\Textarea::make("translations.{$locale}.subtitle")
                    ->label('Deskripsi singkat')
                    ->rows(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make("translations.{$locale}.cta_label")
                    ->label('Label tombol')
                    ->maxLength(60)
                    ->placeholder('Jelajahi Produk'),
            ])->columns(2),
            Forms\Components\Section::make('Tampilan & publikasi')->schema([
                Forms\Components\Select::make('theme')
                    ->label('Warna latar')
                    ->options(AffiliateBanner::themeOptions())
                    ->default('brand')
                    ->required(),
                Forms\Components\TextInput::make('link_url')
                    ->label('Tautan tombol')
                    ->url()
                    ->rules(['url:http,https'])
                    ->maxLength(2048)
                    ->helperText('Boleh dikosongkan. Tanpa tautan, tombol menuju halaman semua produk.'),
                MediaImagePickerField::make('image_id')
                    ->label('Gambar latar (opsional)')
                    ->helperText('Tanpa gambar, slide memakai gradien warna yang dipilih.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Tampilkan di slider')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Slide')
                    ->getStateUsing(fn (AffiliateBanner $record): string => (string) (
                        $record->translate(app()->getLocale(), false)?->title
                        ?? $record->translate('id', false)?->title
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('badge')
                    ->label('Label')
                    ->getStateUsing(fn (AffiliateBanner $record): ?string => $record->translate(app()->getLocale(), false)?->badge)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('theme')
                    ->label('Warna')
                    ->formatStateUsing(fn (?string $state): string => AffiliateBanner::themeOptions()[$state] ?? (string) $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
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
            'index' => Pages\ListAffiliateBanners::route('/'),
            'create' => Pages\CreateAffiliateBanner::route('/create'),
            'edit' => Pages\EditAffiliateBanner::route('/{record}/edit'),
        ];
    }
}

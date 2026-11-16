<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource\Pages;
use App\Domain\Affiliate\Models\AffiliateClick;
use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AffiliateTrafficSourceResource extends Resource
{
    protected static ?string $model = AffiliateTrafficSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Sumber Traffic';

    protected static ?string $pluralModelLabel = 'Sumber Traffic';

    protected static ?string $slug = 'affiliate-sources';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label('Kode sumber (src)')
                ->required()
                ->regex('/^[a-z0-9_-]+$/')
                ->maxLength(64)
                ->unique(ignoreRecord: true)
                ->helperText('Dipakai di URL: ?src=kode_ini. Huruf kecil, angka, garis bawah, atau tanda hubung.')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('label')
                ->label('Nama tampilan')
                ->maxLength(120)
                ->placeholder('Contoh: Facebook Grup Gadget')
                ->helperText('Nama ini muncul di laporan dan pilihan Link Builder.'),
            Forms\Components\TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0)
                ->helperText('Angka lebih kecil tampil lebih dulu.'),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->helperText('Sumber nonaktif tidak muncul di saran Link Builder.'),
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->rows(3)
                ->columnSpanFull()
                ->helperText('Misalnya: grup Facebook khusus gadget, adminnya siapa, atau jadwal posting.'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Nama tampilan')
                    ->searchable()
                    ->placeholder('-')
                    ->wrap(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Total klik')
                    ->getStateUsing(fn (AffiliateTrafficSource $record): int => AffiliateClick::query()
                        ->where('source', $record->code)
                        ->count())
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalDescription('Klik lama tetap menyimpan kode sumbernya. Menghapus di sini hanya menghilangkan saran dan label.'),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Belum ada sumber traffic')
            ->emptyStateDescription('Tambahkan kode seperti fb_grup_gadget agar muncul di Link Builder.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateTrafficSources::route('/'),
            'create' => Pages\CreateAffiliateTrafficSource::route('/create'),
            'edit' => Pages\EditAffiliateTrafficSource::route('/{record}/edit'),
        ];
    }
}

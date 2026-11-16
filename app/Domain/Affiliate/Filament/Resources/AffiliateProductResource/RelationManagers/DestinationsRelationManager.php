<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\RelationManagers;

use App\Domain\Affiliate\Models\AffiliateDestination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DestinationsRelationManager extends RelationManager
{
    protected static string $relationship = 'destinations';

    protected static ?string $title = 'Tujuan Link (Smart Link)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')
                ->label('Label')
                ->required()
                ->maxLength(120)
                ->default('Tujuan utama'),
            Forms\Components\TextInput::make('url')
                ->label('URL tujuan')
                ->required()
                ->url()
                ->rules(['url:http,https'])
                ->maxLength(2048)
                ->helperText('Link marketplace yang dipakai saat pengunjung menekan tombol beli.')
                ->columnSpanFull(),
            Forms\Components\Select::make('status')
                ->label('Status manual')
                ->options(AffiliateDestination::STATUS_OPTIONS)
                ->default(AffiliateDestination::STATUS_ACTIVE)
                ->required()
                ->native(false)
                ->helperText('Stok tidak dideteksi otomatis. Ubah status di sini saat link perlu diganti.'),
            Forms\Components\Toggle::make('is_primary')
                ->label('Tujuan utama')
                ->helperText('Hanya satu tujuan utama per produk.'),
            Forms\Components\TextInput::make('priority')
                ->label('Prioritas')
                ->numeric()
                ->default(0)
                ->helperText('Angka lebih kecil dipilih lebih dulu.'),
            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Mulai berlaku')
                ->seconds(false)
                ->nullable(),
            Forms\Components\DateTimePicker::make('ends_at')
                ->label('Berakhir')
                ->seconds(false)
                ->nullable(),
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Utama')
                    ->boolean(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->description(fn (AffiliateDestination $record): string => Str::limit($record->url, 70)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AffiliateDestination::STATUS_OPTIONS[$state] ?? $state)
                    ->color(fn (AffiliateDestination $record): string => $record->statusColor()),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_primary', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah tujuan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('test')
                    ->label('Test link')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (AffiliateDestination $record): string => $record->url)
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('history')
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->modalHeading('Riwayat perubahan tujuan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (AffiliateDestination $record) => view('filament.affiliate.destination-history', [
                        'destination' => $record,
                    ])),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ]);
    }
}

<?php

namespace App\Domain\Language\Filament\Resources;

use App\Domain\Language\Filament\Resources\LanguageResource\Pages;
use App\Domain\Language\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Bahasa';

    protected static ?string $pluralModelLabel = 'Bahasa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bahasa')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(5)
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('native_name')
                            ->label('Nama native')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('flag')
                            ->label('Bendera')
                            ->maxLength(255)
                            ->placeholder('🇮🇩'),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Default')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Select::make('direction')
                            ->label('Arah teks')
                            ->options([
                                'ltr' => 'LTR',
                                'rtl' => 'RTL',
                            ])
                            ->default('ltr')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flag')
                    ->label('Bendera'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('native_name')
                    ->label('Native')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('setAsDefault')
                    ->label('Set as Default')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (Language $record): bool => ! $record->is_default)
                    ->requiresConfirmation()
                    ->action(function (Language $record): void {
                        DB::transaction(function () use ($record): void {
                            Language::query()->where('is_default', true)->update(['is_default' => false]);
                            $record->update(['is_default' => true]);
                        });

                        Notification::make()
                            ->title('Bahasa default diperbarui')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}

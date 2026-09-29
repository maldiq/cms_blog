<?php

namespace App\Domain\Team\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Team\Filament\Resources\TeamResource\Pages;
use App\Domain\Team\Models\Team;
use App\Domain\Theme\Filament\Forms\Components\MediaPickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Tim';

    protected static ?string $slug = 'teams';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.name")
                    ->label('Nama')
                    ->required($locale === 'id')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")->label('Slug'),
                Forms\Components\TextInput::make("translations.{$locale}.position")->label('Posisi'),
                Forms\Components\Textarea::make("translations.{$locale}.bio")->label('Bio')->rows(4),
            ]),
            Forms\Components\Section::make('Pengaturan')->schema([
                MediaPickerField::make('photo_id')->label('Foto'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\KeyValue::make('social_links')->label('Social links'),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(fn (Team $record): string => (string) $record->translate(app()->getLocale(), false)?->name),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}

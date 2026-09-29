<?php

namespace App\Domain\Gallery\Album\Filament\Resources\AlbumResource\RelationManagers;

use App\Domain\Media\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Foto dalam album';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('caption')
                ->label('Caption')
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('preview')
                    ->label('Preview')
                    ->getStateUsing(fn (Media $record): string => $record->getUrl('thumb')),
                Tables\Columns\TextColumn::make('name')->label('Nama file')->searchable(),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->getStateUsing(fn (Media $record): ?string => $record->pivot?->caption),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('mime_type', 'like', 'image/%')->latest())
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('caption')
                            ->label('Caption')
                            ->maxLength(255),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ]);
    }
}

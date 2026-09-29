<?php

namespace App\Domain\Blog\Series\Filament\Resources\SeriesResource\RelationManagers;

use App\Domain\Blog\Post\Models\Post;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static ?string $title = 'Post dalam series';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Post $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('series_order')->label('Urutan'),
            ])
            ->defaultSort('series_order')
            ->reorderable('series_order')
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    ->recordSelectOptionsQuery(fn ($query) => $query->whereNull('series_id')->latest()),
            ])
            ->actions([
                Tables\Actions\DissociateAction::make(),
            ]);
    }
}

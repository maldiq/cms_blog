<?php

namespace App\Domain\Blog\Post\Filament\Resources\PostResource\RelationManagers;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Media\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class GalleryRelationManager extends RelationManager
{
    protected static string $relationship = 'galleryMedia';

    protected static ?string $title = 'Galeri';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('preview')
                    ->label('Gambar')
                    ->getStateUsing(fn (Media $record): string => $record->hasGeneratedConversion('thumb')
                        ? $record->getFullUrl('thumb')
                        : $record->getFullUrl()),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('human_size')
                    ->label('Ukuran')
                    ->state(fn (Media $record): string => sprintf('%.1f KB', $record->size / 1024)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('order_column')
            ->headerActions([
                Action::make('upload')
                    ->label('Unggah gambar')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Unggah gambar galeri')
                    ->form([
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Gambar')
                            ->multiple()
                            ->image()
                            ->maxFiles(10)
                            ->maxSize(10240)
                            ->disk('public')
                            ->directory('blog-gallery')
                            ->visibility('public'),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        /** @var Post $post */
                        $post = $livewire->getOwnerRecord();
                        $disk = Storage::disk('public');

                        foreach ((array) ($data['attachments'] ?? []) as $path) {
                            if (blank($path) || ! $disk->exists($path)) {
                                continue;
                            }

                            $post->addMedia($disk->path($path))->toMediaCollection('gallery');

                            // Berkas sumber sudah tersalin ke media library; hapus
                            // sementaraannya agar tidak menumpuk di disk.
                            $disk->delete($path);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ]);
    }
}

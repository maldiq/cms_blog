<?php

namespace App\Filament\Widgets;

use App\Domain\Affiliate\Filament\Resources\AffiliateProductResource;
use App\Domain\Affiliate\Models\AffiliateProduct;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestAffiliateProductsTable extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $locale = app()->getLocale();

        return $table
            ->query(
                AffiliateProduct::query()
                    ->with([
                        'image',
                        'translations',
                        'affiliateCategory.translations',
                        'affiliatePlatform.translations',
                    ])
            )
            ->heading('Produk Affiliate Terbaru')
            ->columns([
                Tables\Columns\ImageColumn::make('image_preview')
                    ->label('Gambar')
                    ->getStateUsing(function (AffiliateProduct $record): ?string {
                        $media = $record->image;

                        if ($media === null) {
                            return null;
                        }

                        return $media->hasGeneratedConversion('thumb')
                            ? $media->getUrl('thumb')
                            : $media->getUrl();
                    })
                    ->square()
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->label('Produk')
                    ->getStateUsing(fn (AffiliateProduct $record): string => (string) (
                        $record->translate($locale, false)?->title
                        ?? $record->translate('id', false)?->title
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
                    })
                    ->limit(45)
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('affiliateCategory.name')
                    ->label('Kategori')
                    ->getStateUsing(fn (AffiliateProduct $record): string => (string) (
                        $record->affiliateCategory?->translate($locale, false)?->name
                        ?? $record->affiliateCategory?->translate('id', false)?->name
                    ))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('affiliatePlatform.name')
                    ->label('Platform')
                    ->getStateUsing(fn (AffiliateProduct $record): string => (string) (
                        $record->affiliatePlatform?->translate($locale, false)?->name
                        ?? $record->affiliatePlatform?->translate('id', false)?->name
                    ))
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state): string => format_price($state, 'id') ?? '—'),
                Tables\Columns\TextColumn::make('click_count')
                    ->label('Klik')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (AffiliateProduct $record): string => AffiliateProductResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum ada produk affiliate')
            ->emptyStateDescription('Tambahkan produk pertama dari menu Produk Affiliate.');
    }
}

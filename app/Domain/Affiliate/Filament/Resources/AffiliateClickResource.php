<?php

namespace App\Domain\Affiliate\Filament\Resources;

use App\Domain\Affiliate\Filament\Resources\AffiliateClickResource\Pages;
use App\Domain\Affiliate\Models\AffiliateClick;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use App\Domain\Language\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliateClickResource extends Resource
{
    protected static ?string $model = AffiliateClick::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'Klik Affiliate';

    protected static ?string $pluralModelLabel = 'Klik Affiliate';

    protected static ?string $slug = 'affiliate-clicks';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = AffiliateClick::query()->whereDate('clicked_at', today())->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clicked_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_title')
                    ->label('Produk')
                    ->getStateUsing(fn (AffiliateClick $record): string => (string) (
                        $record->product?->translate('id', false)?->title
                        ?? $record->product?->translate('en', false)?->title
                        ?? '-'
                    ))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('product.translations', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        if (blank($state)) {
                            return AffiliateClick::SOURCE_DIRECT;
                        }

                        return AffiliateTrafficSource::labelMap()[$state] ?? $state;
                    })
                    ->description(fn (AffiliateClick $record): ?string => filled($record->source)
                        && isset(AffiliateTrafficSource::labelMap()[$record->source])
                            ? $record->source
                            : null)
                    ->color(fn (?string $state): string => $state === null ? 'gray' : 'success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.affiliatePlatform.slug')
                    ->label('Platform')
                    ->badge()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('destination.label')
                    ->label('Tujuan')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('locale')
                    ->label('Locale')
                    ->badge(),
                Tables\Columns\TextColumn::make('referrer_host')
                    ->label('Referrer')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user_agent_family')
                    ->label('Browser')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('first_source')
                    ->label('Sumber pertama')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('click_id')
                    ->label('Click ID')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('clicked_at', 'desc')
            ->filters([
                SelectFilter::make('affiliate_product_id')
                    ->label('Produk')
                    ->searchable()
                    ->options(fn (): array => AffiliateProduct::query()
                        ->with('translations')
                        ->orderByDesc('created_at')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn (AffiliateProduct $product): array => [
                            $product->getKey() => (string) (
                                $product->translate('id', false)?->title
                                ?? $product->translate('en', false)?->title
                                ?? '#'.$product->getKey()
                            ),
                        ])
                        ->all()),
                SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(function (): array {
                        $saved = AffiliateTrafficSource::query()
                            ->ordered()
                            ->get()
                            ->mapWithKeys(fn (AffiliateTrafficSource $source): array => [
                                $source->code => $source->displayName(),
                            ])
                            ->all();

                        $used = AffiliateClick::query()
                            ->whereNotNull('source')
                            ->distinct()
                            ->orderBy('source')
                            ->pluck('source', 'source')
                            ->all();

                        return $saved + $used;
                    }),
                SelectFilter::make('locale')
                    ->label('Locale')
                    ->options(fn (): array => Language::getActive()->pluck('code', 'code')->all()),
                SelectFilter::make('platform')
                    ->label('Platform')
                    ->options(fn (): array => AffiliatePlatform::query()
                        ->with('translations')
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (AffiliatePlatform $platform): array => [
                            $platform->slug => (string) (
                                $platform->translate('id', false)?->name
                                ?? $platform->translate('en', false)?->name
                                ?? $platform->slug
                            ),
                        ])
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        $slug = $data['value'] ?? null;

                        return $query->when(
                            filled($slug),
                            fn (Builder $query) => $query->whereHas(
                                'product.affiliatePlatform',
                                fn (Builder $query) => $query->where('slug', $slug),
                            ),
                        );
                    }),
                Filter::make('clicked_at')
                    ->label('Rentang tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('clicked_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('clicked_at', '<=', $date));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateClicks::route('/'),
        ];
    }
}

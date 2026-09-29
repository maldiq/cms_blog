<?php

namespace App\Domain\Theme\Filament\Forms\Components;

use App\Domain\Media\Models\Media;
use Filament\Forms\Components\Select;

class MediaPickerField extends Select
{
    public static function make(string $name): static
    {
        return parent::make($name)
            ->label('Media')
            ->searchable()
            ->preload()
            ->options(
                fn (): array => Media::query()
                    ->orderBy('name')
                    ->limit(100)
                    ->pluck('name', 'id')
                    ->all()
            )
            ->getSearchResultsUsing(
                fn (string $search): array => Media::query()
                    ->where('name', 'like', '%' . $search . '%')
                    ->orderBy('name')
                    ->limit(50)
                    ->pluck('name', 'id')
                    ->all()
            )
            ->getOptionLabelUsing(
                fn ($value): ?string => filled($value)
                    ? Media::query()->find($value)?->name
                    : null
            )
            ->native(false);
    }
}

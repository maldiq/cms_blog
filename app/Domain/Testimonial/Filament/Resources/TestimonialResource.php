<?php

namespace App\Domain\Testimonial\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Testimonial\Filament\Resources\TestimonialResource\Pages;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Theme\Filament\Forms\Components\MediaPickerField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Testimonial';

    protected static ?string $slug = 'testimonials';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.author_name")->label('Nama')->required($locale === 'id'),
                Forms\Components\TextInput::make("translations.{$locale}.author_position")->label('Posisi'),
                Forms\Components\TextInput::make("translations.{$locale}.author_company")->label('Perusahaan'),
                Forms\Components\Textarea::make("translations.{$locale}.content")->label('Kutipan')->rows(4),
            ]),
            Forms\Components\Section::make('Pengaturan')->schema([
                MediaPickerField::make('photo_id')->label('Foto'),
                Forms\Components\Select::make('rating')
                    ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                    ->default(5)
                    ->required(),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Nama')
                    ->getStateUsing(fn (Testimonial $record): string => (string) $record->translate(app()->getLocale(), false)?->author_name),
                Tables\Columns\TextColumn::make('rating'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Domain\Page\Filament\Resources;

use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\Page\Filament\Resources\PageResource\Pages;
use App\Domain\Page\Models\Page;
use App\Domain\User\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman';

    protected static ?string $navigationLabel = 'Halaman';

    protected static ?string $slug = 'pages';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(fn (string $locale): array => [
                Forms\Components\TextInput::make("translations.{$locale}.title")
                    ->label('Judul')
                    ->required($locale === 'id')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state))),
                Forms\Components\TextInput::make("translations.{$locale}.slug")
                    ->label('Slug')
                    ->maxLength(255),
                Forms\Components\RichEditor::make("translations.{$locale}.content")
                    ->label('Konten')
                    ->columnSpanFull()
                    ->saveUploadedFileAttachmentsUsing(function (TemporaryUploadedFile $file): string {
                        $media = app(MediaUploadService::class)->uploadFile($file);

                        return $media->getFullUrl();
                    }),
                Forms\Components\TextInput::make("translations.{$locale}.meta_title")
                    ->label('Meta title')
                    ->maxLength(255),
                Forms\Components\TextInput::make("translations.{$locale}.meta_description")
                    ->label('Meta description')
                    ->maxLength(255),
            ]),
            Forms\Components\Section::make('Pengaturan')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Penulis')
                        ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                        ->required()
                        ->default(fn (): ?int => auth()->id()),
                    Forms\Components\Select::make('template')
                        ->label('Template')
                        ->options([
                            'default' => 'Default',
                            'contact' => 'Contact',
                        ])
                        ->default('default')
                        ->required(),
                    Forms\Components\Toggle::make('is_homepage')
                        ->label('Jadikan homepage')
                        ->helperText('Hanya satu halaman aktif sebagai homepage per locale route.'),
                    Forms\Components\Select::make('status')
                        ->options([
                            Page::STATUS_DRAFT => 'Draft',
                            Page::STATUS_PUBLISHED => 'Published',
                        ])
                        ->required(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Published at'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Page $record): string => (string) $record->translate(app()->getLocale(), false)?->title),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_homepage')->label('Homepage')->boolean(),
                Tables\Columns\TextColumn::make('template')->label('Template'),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}

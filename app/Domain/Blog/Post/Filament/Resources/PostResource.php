<?php

namespace App\Domain\Blog\Post\Filament\Resources;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Filament\Concerns\InteractsWithLocaleTabs;
use App\Domain\Blog\Post\Filament\Resources\PostResource\Pages;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\User\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostResource extends Resource
{
    use InteractsWithLocaleTabs;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?string $modelLabel = 'Post';

    protected static ?string $slug = 'posts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            static::localeTabs(function (string $locale): array {
                return [
                    Forms\Components\TextInput::make("translations.{$locale}.title")
                        ->label('Judul')
                        ->required($locale === 'id')
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set("translations.{$locale}.slug", \Illuminate\Support\Str::slug((string) $state))),
                    Forms\Components\TextInput::make("translations.{$locale}.slug")->label('Slug'),
                    Forms\Components\Textarea::make("translations.{$locale}.excerpt")->label('Excerpt')->rows(3),
                    Forms\Components\RichEditor::make("translations.{$locale}.content")
                        ->label('Konten')
                        ->columnSpanFull()
                        ->saveUploadedFileAttachmentsUsing(function (TemporaryUploadedFile $file): string {
                            $media = app(MediaUploadService::class)->uploadFile($file);

                            return $media->getFullUrl();
                        }),
                    Forms\Components\TextInput::make("translations.{$locale}.meta_title")->label('Meta title'),
                    Forms\Components\TextInput::make("translations.{$locale}.meta_description")->label('Meta description'),
                    Forms\Components\TextInput::make("translations.{$locale}.meta_keywords")->label('Meta keywords'),
                    Forms\Components\Select::make("translations.{$locale}.og_image_id")
                        ->label('OG Image')
                        ->searchable()
                        ->options(fn (): array => \App\Domain\Media\Models\Media::query()->latest()->limit(100)->pluck('name', 'id')->all()),
                ];
            }),
            Forms\Components\Section::make('Post')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Author')
                        ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                        ->required()
                        ->default(fn (): ?int => auth()->id()),
                    Forms\Components\Select::make('type')
                        ->options([
                            'article' => 'Article',
                            'tutorial' => 'Tutorial',
                            'news' => 'News',
                        ])
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            Post::STATUS_DRAFT => 'Draft',
                            Post::STATUS_REVIEW => 'Review',
                            Post::STATUS_PUBLISHED => 'Published',
                            Post::STATUS_SCHEDULED => 'Scheduled',
                            Post::STATUS_ARCHIVED => 'Archived',
                        ])
                        ->required(),
                    Forms\Components\Select::make('featured_image_id')
                        ->label('Featured image')
                        ->searchable()
                        ->options(fn (): array => \App\Domain\Media\Models\Media::query()->latest()->limit(100)->pluck('name', 'id')->all()),
                    Forms\Components\FileUpload::make('gallery_uploads')
                        ->label('Gallery')
                        ->multiple()
                        ->disk('public')
                        ->directory('blog-gallery')
                        ->visibility('public'),
                    Forms\Components\Select::make('series_id')
                        ->label('Series')
                        ->options(fn (): array => Series::query()->get()->mapWithKeys(fn (Series $series): array => [
                            $series->id => (string) $series->translate('id', false)?->title,
                        ])->all())
                        ->nullable(),
                    Forms\Components\TextInput::make('series_order')->numeric()->label('Series order'),
                    Forms\Components\DateTimePicker::make('published_at')->label('Published at'),
                    Forms\Components\Toggle::make('is_featured')->label('Featured'),
                    Forms\Components\Toggle::make('allow_comment')->label('Allow comment')->default(true),
                    Forms\Components\TextInput::make('template')->label('Template'),
                    Forms\Components\Select::make('categories')
                        ->label('Kategori')
                        ->multiple()
                        ->relationship('categories', 'id')
                        ->getOptionLabelFromRecordUsing(fn (Category $record): string => (string) $record->translate('id', false)?->name),
                    Forms\Components\Select::make('tags')
                        ->label('Tag')
                        ->multiple()
                        ->relationship('tags', 'id')
                        ->getOptionLabelFromRecordUsing(fn (Tag $record): string => (string) $record->translate('id', false)?->name),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image_preview')
                    ->label('Gambar')
                    ->getStateUsing(fn (Post $record): ?string => $record->featuredImage?->getFullUrl())
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->getStateUsing(fn (Post $record): string => (string) $record->translate(app()->getLocale(), false)?->title)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', fn (Builder $q) => $q->where('title', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('user.name')->label('Author'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->getStateUsing(fn (Post $record): array => $record->categories->map(fn (Category $c): string => (string) $c->translate(app()->getLocale(), false)?->name)->all()),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('view_count')->label('Views'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    Post::STATUS_DRAFT => 'Draft',
                    Post::STATUS_PUBLISHED => 'Published',
                    Post::STATUS_REVIEW => 'Review',
                    Post::STATUS_SCHEDULED => 'Scheduled',
                    Post::STATUS_ARCHIVED => 'Archived',
                ]),
                Tables\Filters\SelectFilter::make('type')->options([
                    'article' => 'Article',
                    'tutorial' => 'Tutorial',
                    'news' => 'News',
                ]),
                Tables\Filters\SelectFilter::make('user_id')->label('Author')->relationship('user', 'name'),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('published_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('published_at', '<=', $date));
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each(fn (Post $post) => $post->update(['status' => Post::STATUS_PUBLISHED, 'published_at' => $post->published_at ?? now()]))),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->action(fn ($records) => $records->each(fn (Post $post) => $post->update(['status' => Post::STATUS_DRAFT]))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

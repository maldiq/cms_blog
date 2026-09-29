<?php

namespace App\Domain\Comment\Filament\Resources;

use App\Domain\Blog\Post\Filament\Resources\PostResource;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Comment\Filament\Resources\CommentResource\Pages;
use App\Domain\Comment\Models\Comment;
use App\Domain\Comment\Services\CommentService;
use App\Domain\User\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Komentar';

    protected static ?string $slug = 'comments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    Comment::STATUS_PENDING => 'Pending',
                    Comment::STATUS_APPROVED => 'Approved',
                    Comment::STATUS_SPAM => 'Spam',
                    Comment::STATUS_TRASH => 'Trash',
                ])
                ->required(),
            Forms\Components\Textarea::make('content')
                ->label('Konten')
                ->required()
                ->rows(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->label('Penulis')
                    ->getStateUsing(fn (Comment $record): string => $record->authorDisplayName())
                    ->description(fn (Comment $record): ?string => $record->author_email),
                Tables\Columns\TextColumn::make('content')
                    ->label('Konten')
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Comment::STATUS_APPROVED => 'success',
                        Comment::STATUS_PENDING => 'warning',
                        Comment::STATUS_SPAM => 'danger',
                        Comment::STATUS_TRASH => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('commentable')
                    ->label('Pada')
                    ->getStateUsing(function (Comment $record): string {
                        $target = $record->commentable;

                        if ($target instanceof Post) {
                            return (string) $target->translate(app()->getLocale(), false)?->title;
                        }

                        return class_basename((string) $record->commentable_type) . ' #' . $record->commentable_id;
                    })
                    ->url(function (Comment $record): ?string {
                        if ($record->commentable instanceof Post) {
                            return PostResource::getUrl('edit', ['record' => $record->commentable]);
                        }

                        return null;
                    }),
                Tables\Columns\IconColumn::make('is_pinned')->label('Pin')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Comment::STATUS_PENDING => 'Pending',
                        Comment::STATUS_APPROVED => 'Approved',
                        Comment::STATUS_SPAM => 'Spam',
                        Comment::STATUS_TRASH => 'Trash',
                    ]),
                SelectFilter::make('commentable_type')
                    ->label('Tipe')
                    ->options([
                        Post::class => 'Post',
                    ]),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Balas')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->form([
                        Forms\Components\Textarea::make('content')
                            ->label('Balasan admin')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Comment $record, array $data): void {
                        /** @var User $user */
                        $user = auth()->user();
                        $commentable = $record->commentable;

                        if ($commentable === null) {
                            return;
                        }

                        $reply = app(CommentService::class)->createComment([
                            'parent_id' => $record->id,
                            'user_id' => $user->id,
                            'author_name' => $user->name,
                            'author_email' => $user->email,
                            'author_ip' => request()->ip() ?? '127.0.0.1',
                            'user_agent' => request()->userAgent(),
                            'content' => $data['content'],
                            'force_spam' => false,
                        ], $commentable);

                        app(CommentService::class)->moderate($reply, Comment::STATUS_APPROVED);
                    }),
                Tables\Actions\Action::make('togglePin')
                    ->label(fn (Comment $record): string => $record->is_pinned ? 'Unpin' : 'Pin')
                    ->icon('heroicon-o-bookmark')
                    ->action(fn (Comment $record) => $record->update(['is_pinned' => ! $record->is_pinned])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each(
                            fn (Comment $comment) => app(CommentService::class)->moderate($comment, Comment::STATUS_APPROVED)
                        )),
                    Tables\Actions\BulkAction::make('spam')
                        ->label('Mark as spam')
                        ->icon('heroicon-o-no-symbol')
                        ->action(fn ($records) => $records->each(
                            fn (Comment $comment) => app(CommentService::class)->moderate($comment, Comment::STATUS_SPAM)
                        )),
                    Tables\Actions\BulkAction::make('trash')
                        ->label('Trash')
                        ->icon('heroicon-o-trash')
                        ->action(fn ($records) => $records->each(
                            fn (Comment $comment) => app(CommentService::class)->moderate($comment, Comment::STATUS_TRASH)
                        )),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}

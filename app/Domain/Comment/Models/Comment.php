<?php

namespace App\Domain\Comment\Models;

use App\Domain\Comment\Policies\CommentPolicy;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(CommentPolicy::class)]
class Comment extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_SPAM = 'spam';

    public const STATUS_TRASH = 'trash';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'parent_id',
        'user_id',
        'author_name',
        'author_email',
        'author_url',
        'author_ip',
        'user_agent',
        'content',
        'status',
        'is_pinned',
        'edited_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'edited_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderByDesc('is_pinned')
            ->orderBy('created_at');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasAnyRole(['super-admin', 'admin', 'editor'])) {
            return true;
        }

        return $this->user_id !== null && $this->user_id === $user->id;
    }

    public function authorDisplayName(): string
    {
        if ($this->user !== null) {
            return $this->user->name;
        }

        return (string) ($this->author_name ?: 'Tamu');
    }
}

<?php

namespace App\Domain\Comment\Services;

use App\Domain\Comment\Events\CommentCreated;
use App\Domain\Comment\Models\Comment;
use App\Domain\Setting\Settings\CommentSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CommentService
{
    public function __construct(
        private readonly CommentSettings $commentSettings,
    ) {}

    /**
     * @return Collection<int, Comment>
     */
    public function getApprovedTree(Model $commentable): Collection
    {
        $maxDepth = max(1, $this->commentSettings->max_depth);

        $roots = Comment::query()
            ->where('commentable_type', $commentable->getMorphClass())
            ->where('commentable_id', $commentable->getKey())
            ->approved()
            ->root()
            ->orderByDesc('is_pinned')
            ->orderBy('created_at')
            ->get();

        $this->loadApprovedChildren($roots, 1, $maxDepth);

        return $roots;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createComment(array $data, Model $commentable): Comment
    {
        $parentId = $data['parent_id'] ?? null;

        if ($parentId !== null) {
            $parent = Comment::query()->findOrFail($parentId);

            if ($parent->commentable_type !== $commentable->getMorphClass()
                || (int) $parent->commentable_id !== (int) $commentable->getKey()) {
                throw new InvalidArgumentException('Parent comment tidak valid.');
            }

            $depth = $this->depthOf($parent);

            if ($depth >= $this->commentSettings->max_depth) {
                throw new InvalidArgumentException('Kedalaman balasan melebihi batas.');
            }
        }

        $status = $this->resolveStatus($data);

        $comment = Comment::query()->create([
            'commentable_type' => $commentable->getMorphClass(),
            'commentable_id' => $commentable->getKey(),
            'parent_id' => $parentId,
            'user_id' => $data['user_id'] ?? null,
            'author_name' => $data['author_name'] ?? null,
            'author_email' => $data['author_email'] ?? null,
            'author_url' => $data['author_url'] ?? null,
            'author_ip' => (string) ($data['author_ip'] ?? '0.0.0.0'),
            'user_agent' => $data['user_agent'] ?? null,
            'content' => trim((string) ($data['content'] ?? '')),
            'status' => $status,
            'is_pinned' => (bool) ($data['is_pinned'] ?? false),
        ]);

        if ($status !== Comment::STATUS_SPAM) {
            CommentCreated::dispatch($comment);
        }

        return $comment;
    }

    public function moderate(Comment $comment, string $status): void
    {
        if (! in_array($status, [
            Comment::STATUS_PENDING,
            Comment::STATUS_APPROVED,
            Comment::STATUS_SPAM,
            Comment::STATUS_TRASH,
        ], true)) {
            throw new InvalidArgumentException('Status moderasi tidak valid.');
        }

        $comment->update(['status' => $status]);
    }

    /**
     * @param  Collection<int, Comment>  $comments
     */
    protected function loadApprovedChildren(Collection $comments, int $currentDepth, int $maxDepth): void
    {
        if ($currentDepth > $maxDepth || $comments->isEmpty()) {
            return;
        }

        $comments->load([
            'children' => fn ($query) => $query->approved()->orderByDesc('is_pinned')->orderBy('created_at'),
        ]);

        $children = $comments->pluck('children')->flatten();

        if ($children->isNotEmpty()) {
            $this->loadApprovedChildren($children, $currentDepth + 1, $maxDepth);
        }
    }

    protected function depthOf(Comment $comment): int
    {
        $depth = 1;
        $current = $comment;

        while ($current->parent_id !== null) {
            $current = $current->parent()->first();

            if ($current === null) {
                break;
            }

            $depth++;
        }

        return $depth;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveStatus(array $data): string
    {
        if (($data['force_spam'] ?? false) === true) {
            return Comment::STATUS_SPAM;
        }

        if ($this->containsBlockedWords((string) ($data['content'] ?? ''))) {
            return Comment::STATUS_SPAM;
        }

        if ($this->commentSettings->auto_approve) {
            return Comment::STATUS_APPROVED;
        }

        return Comment::STATUS_PENDING;
    }

    protected function containsBlockedWords(string $content): bool
    {
        $raw = $this->commentSettings->blocked_words ?? '';

        if (blank($raw)) {
            return false;
        }

        $words = array_filter(array_map('trim', explode(',', Str::lower($raw))));

        if ($words === []) {
            return false;
        }

        $haystack = Str::lower($content);

        foreach ($words as $word) {
            if ($word !== '' && str_contains($haystack, $word)) {
                return true;
            }
        }

        return false;
    }
}

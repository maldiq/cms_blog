<?php

namespace App\Domain\Comment\Livewire;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Comment\Services\CommentService;
use App\Domain\Setting\Settings\CommentSettings;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentSection extends Component
{
    public string $commentableType;

    public int $commentableId;

    /** @var list<int> */
    public array $replyOpenFor = [];

    public function mount(string $commentableType, int $commentableId): void
    {
        $this->commentableType = $commentableType;
        $this->commentableId = $commentableId;
    }

    #[On('comment-submitted')]
    public function refreshComments(): void
    {
        $this->submitted = true;
    }

    public bool $submitted = false;

    public function toggleReply(int $commentId): void
    {
        if (in_array($commentId, $this->replyOpenFor, true)) {
            $this->replyOpenFor = array_values(array_diff($this->replyOpenFor, [$commentId]));
        } else {
            $this->replyOpenFor[] = $commentId;
        }
    }

    public function render(CommentService $commentService, CommentSettings $commentSettings)
    {
        $commentable = $this->commentableType === Post::class
            ? Post::query()->find($this->commentableId)
            : null;

        $comments = $commentable !== null
            ? $commentService->getApprovedTree($commentable)
            : collect();

        return view('domain.comment.livewire.comment-section', [
            'comments' => $comments,
            'maxDepth' => $commentSettings->max_depth,
        ]);
    }
}

<?php

namespace App\Domain\Comment\Livewire;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Comment\Models\Comment;
use App\Domain\Comment\Services\CommentService;
use App\Domain\Setting\Settings\CommentSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class CommentForm extends Component
{
    public string $commentableType;

    public int $commentableId;

    public ?int $parentId = null;

    public string $authorName = '';

    public string $authorEmail = '';

    public string $authorUrl = '';

    public string $content = '';

    /** Honeypot — harus kosong */
    public string $website = '';

    public bool $submitted = false;

    public ?string $errorMessage = null;

    public function mount(string $commentableType, int $commentableId, ?int $parentId = null): void
    {
        $this->commentableType = $commentableType;
        $this->commentableId = $commentableId;
        $this->parentId = $parentId;

        $user = auth()->user();

        if ($user !== null) {
            $this->authorName = $user->name;
            $this->authorEmail = (string) $user->email;
        }
    }

    public function submit(CommentService $commentService, CommentSettings $commentSettings): void
    {
        $this->errorMessage = null;

        $this->validate([
            'content' => ['required', 'string', 'min:2', 'max:5000'],
            'authorName' => ['required', 'string', 'max:255'],
            'authorEmail' => [$commentSettings->require_email ? 'required' : 'nullable', 'email', 'max:255'],
            'authorUrl' => ['nullable', 'url', 'max:255'],
        ], [], [
            'authorName' => 'nama',
            'authorEmail' => 'email',
            'authorUrl' => 'website',
            'content' => 'komentar',
        ]);

        $ip = request()->ip() ?? '0.0.0.0';
        $rateKey = 'comment-submit:' . $ip;

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $this->errorMessage = 'Terlalu banyak komentar. Coba lagi nanti.';

            return;
        }

        RateLimiter::hit($rateKey, 60);

        $commentable = $this->resolveCommentable();

        if ($commentable === null) {
            $this->errorMessage = 'Konten tidak ditemukan.';

            return;
        }

        if ($commentable instanceof Post && ! $commentable->allow_comment) {
            $this->errorMessage = 'Komentar dinonaktifkan untuk post ini.';

            return;
        }

        $forceSpam = filled($this->website);

        try {
            $commentService->createComment([
                'parent_id' => $this->parentId,
                'user_id' => auth()->id(),
                'author_name' => $this->authorName,
                'author_email' => $this->authorEmail ?: null,
                'author_url' => $this->authorUrl ?: null,
                'author_ip' => $ip,
                'user_agent' => request()->userAgent(),
                'content' => $this->content,
                'force_spam' => $forceSpam,
            ], $commentable);
        } catch (\InvalidArgumentException $exception) {
            $this->errorMessage = $exception->getMessage();

            return;
        }

        $this->reset(['content', 'website']);
        $this->submitted = true;

        $this->dispatch('comment-submitted');
    }

    protected function resolveCommentable(): ?Model
    {
        if ($this->commentableType === Post::class) {
            return Post::query()->find($this->commentableId);
        }

        return null;
    }

    public function render()
    {
        return view('domain.comment.livewire.comment-form');
    }
}

<li class="@if ($depth > 1) ml-6 border-l border-gray-200 pl-4 @endif">
    <article class="rounded-lg bg-gray-50 p-4">
        <header class="mb-2 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-semibold text-gray-900">{{ $comment->authorDisplayName() }}</span>
            @if ($comment->is_pinned)
                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-800">Pin</span>
            @endif
            <time class="text-gray-500" datetime="{{ $comment->created_at?->toIso8601String() }}">
                {{ $comment->created_at?->format('d M Y H:i') }}
            </time>
        </header>
        <div class="prose prose-sm max-w-none text-gray-800">
            {!! nl2br(e($comment->content)) !!}
        </div>
        @if ($depth < $maxDepth)
            <button type="button"
                    wire:click="toggleReply({{ $comment->id }})"
                    class="mt-3 text-sm font-medium text-emerald-700 hover:underline">
                Balas
            </button>
        @endif
    </article>

    @if ($depth < $maxDepth && in_array($comment->id, $replyOpenFor, true))
        <div class="mt-4">
            @livewire(\App\Domain\Comment\Livewire\CommentForm::class, [
                'commentableType' => $commentableType,
                'commentableId' => $commentableId,
                'parentId' => $comment->id,
            ], key('comment-form-reply-' . $comment->id))
        </div>
    @endif

    @if ($comment->children->isNotEmpty() && $depth < $maxDepth)
        <ul class="mt-4 space-y-4">
            @foreach ($comment->children as $child)
                @include('domain.comment.partials.comment-item', [
                    'comment' => $child,
                    'depth' => $depth + 1,
                    'maxDepth' => $maxDepth,
                    'commentableType' => $commentableType,
                    'commentableId' => $commentableId,
                    'replyOpenFor' => $replyOpenFor,
                ])
            @endforeach
        </ul>
    @endif
</li>

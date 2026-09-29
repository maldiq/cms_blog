<section class="mt-12 border-t border-gray-200 pt-10">
    <h2 class="mb-6 text-xl font-semibold text-gray-900">Komentar</h2>

    @if ($submitted)
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            Komentar menunggu moderasi. Terima kasih!
        </div>
    @endif

    @if ($comments->isEmpty())
        <p class="mb-6 text-sm text-gray-500">Belum ada komentar. Jadilah yang pertama!</p>
    @else
        <ul class="mb-8 space-y-6">
            @foreach ($comments as $comment)
                @include('domain.comment.partials.comment-item', [
                    'comment' => $comment,
                    'depth' => 1,
                    'maxDepth' => $maxDepth,
                    'commentableType' => $commentableType,
                    'commentableId' => $commentableId,
                    'replyOpenFor' => $replyOpenFor,
                ])
            @endforeach
        </ul>
    @endif

    @livewire(\App\Domain\Comment\Livewire\CommentForm::class, [
        'commentableType' => $commentableType,
        'commentableId' => $commentableId,
    ], key('comment-form-root-' . $commentableId))
</section>

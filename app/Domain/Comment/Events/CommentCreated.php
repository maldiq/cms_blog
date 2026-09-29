<?php

namespace App\Domain\Comment\Events;

use App\Domain\Comment\Models\Comment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Comment $comment,
    ) {}
}

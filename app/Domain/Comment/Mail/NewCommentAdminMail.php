<?php

namespace App\Domain\Comment\Mail;

use App\Domain\Comment\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCommentAdminMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Comment $comment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Komentar baru menunggu moderasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'domain.comment.mail.new-comment-admin',
        );
    }
}

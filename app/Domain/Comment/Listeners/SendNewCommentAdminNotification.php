<?php

namespace App\Domain\Comment\Listeners;

use App\Domain\Comment\Events\CommentCreated;
use App\Domain\Comment\Mail\NewCommentAdminMail;
use App\Domain\Setting\Services\MailSettingsConfigurator;
use App\Domain\Setting\Settings\CommentSettings;
use Illuminate\Support\Facades\Mail;

class SendNewCommentAdminNotification
{
    public function __construct(
        private readonly CommentSettings $commentSettings,
        private readonly MailSettingsConfigurator $mailSettingsConfigurator,
    ) {}

    public function handle(CommentCreated $event): void
    {
        if (! $this->commentSettings->notify_admin) {
            return;
        }

        $recipient = $this->commentSettings->admin_email;

        if (blank($recipient)) {
            return;
        }

        $mailer = $this->mailSettingsConfigurator->registerMailer();

        Mail::mailer($mailer)->to($recipient)->send(new NewCommentAdminMail($event->comment));
    }
}

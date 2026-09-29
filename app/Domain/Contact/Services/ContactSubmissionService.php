<?php

namespace App\Domain\Contact\Services;

use App\Domain\Contact\Mail\NewContactSubmissionMail;
use App\Domain\Contact\Models\ContactSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactSubmissionService
{
    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     phone: ?string,
     *     subject: string,
     *     message: string,
     *     locale: string,
     *     ip_address: ?string,
     *     user_agent: ?string,
     * }  $data
     */
    public function submit(array $data): ContactSubmission
    {
        $submission = ContactSubmission::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'locale' => $data['locale'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'status' => ContactSubmission::STATUS_NEW,
        ]);

        $adminEmail = theme('contact.email');

        if (filled($adminEmail)) {
            Mail::to($adminEmail)->queue(new NewContactSubmissionMail($submission));
        } else {
            Log::warning('Contact submission saved but theme contact.email is empty; admin notification skipped.', [
                'submission_id' => $submission->id,
            ]);
        }

        return $submission;
    }

    public function markStatus(ContactSubmission $submission, string $status): void
    {
        $submission->update(['status' => $status]);
    }
}

<?php

namespace App\Domain\Contact\Policies;

use App\Domain\Contact\Models\ContactSubmission;
use App\Domain\User\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('contact.submission.viewAny');
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('contact.submission.update');
    }

    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('contact.submission.delete');
    }
}

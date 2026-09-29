<?php

namespace App\Domain\Newsletter\Policies;

use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\User\Models\User;

class NewsletterSubscriberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('newsletter.subscriber.viewAny');
    }

    public function view(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('newsletter.subscriber.update');
    }

    public function delete(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('newsletter.subscriber.delete');
    }

    public function export(User $user): bool
    {
        return $this->viewAny($user)
            && ($user->hasAnyRole(['super-admin', 'admin']) || $user->can('newsletter.subscriber.export'));
    }
}

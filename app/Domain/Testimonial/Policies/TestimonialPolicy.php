<?php

namespace App\Domain\Testimonial\Policies;

use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\User\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('testimonial.testimonial.viewAny');
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('testimonial.testimonial.create');
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('testimonial.testimonial.delete');
    }
}

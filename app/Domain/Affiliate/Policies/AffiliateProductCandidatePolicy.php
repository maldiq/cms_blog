<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateProductCandidate;
use App\Domain\User\Models\User;

class AffiliateProductCandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.candidate.viewAny');
    }

    public function view(User $user, AffiliateProductCandidate $candidate): bool
    {
        return $this->viewAny($user);
    }

    public function import(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.candidate.import');
    }

    public function reject(User $user, AffiliateProductCandidate $candidate): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.candidate.reject');
    }

    public function sync(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.candidate.sync');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AffiliateProductCandidate $candidate): bool
    {
        return $this->import($user);
    }

    public function delete(User $user, AffiliateProductCandidate $candidate): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('affiliate.candidate.delete');
    }
}

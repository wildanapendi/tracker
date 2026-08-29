<?php

namespace App\Policies;

use App\Models\ThesisProfile;
use App\Models\User;

/**
 * Policy authorization untuk ThesisProfile.
 * Setiap user hanya bisa akses profil skripsinya sendiri.
 */
class ThesisProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ThesisProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ThesisProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function delete(User $user, ThesisProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

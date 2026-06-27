<?php

namespace App\Policies;

use App\Models\Guidance;
use App\Models\User;

/**
 * Policy authorization untuk Guidance.
 * Mencegah IDOR — user hanya bisa akses bimbingan miliknya sendiri.
 */
class GuidancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Guidance $guidance): bool
    {
        return $user->id === $guidance->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Guidance $guidance): bool
    {
        return $user->id === $guidance->user_id;
    }

    public function delete(User $user, Guidance $guidance): bool
    {
        return $user->id === $guidance->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\User;

/**
 * Policy authorization untuk Milestone.
 * Mencegah IDOR — user hanya bisa akses milestone miliknya sendiri.
 */
class MilestonePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Milestone $milestone): bool
    {
        return $user->id === $milestone->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Milestone $milestone): bool
    {
        return $user->id === $milestone->user_id;
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        return $user->id === $milestone->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

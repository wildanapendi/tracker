<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;

/**
 * Policy authorization untuk Chapter.
 * Mencegah IDOR — user hanya bisa akses chapter miliknya sendiri.
 */
class ChapterPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Chapter $chapter): bool
    {
        return $user->id === $chapter->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Chapter $chapter): bool
    {
        return $user->id === $chapter->user_id;
    }

    public function delete(User $user, Chapter $chapter): bool
    {
        return $user->id === $chapter->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

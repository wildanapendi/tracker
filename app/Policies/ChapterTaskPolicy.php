<?php

namespace App\Policies;

use App\Models\ChapterTask;
use App\Models\User;

/**
 * Policy authorization untuk ChapterTask.
 * Ownership diperiksa melalui relasi chapter → user_id.
 */
class ChapterTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ChapterTask $task): bool
    {
        return $user->id === $task->chapter->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ChapterTask $task): bool
    {
        return $user->id === $task->chapter->user_id;
    }

    public function delete(User $user, ChapterTask $task): bool
    {
        return $user->id === $task->chapter->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

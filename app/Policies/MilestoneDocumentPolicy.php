<?php

namespace App\Policies;

use App\Models\MilestoneDocument;
use App\Models\User;

/**
 * Policy authorization untuk MilestoneDocument.
 * Ownership diperiksa melalui relasi milestone → user_id.
 */
class MilestoneDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MilestoneDocument $document): bool
    {
        return $user->id === $document->milestone->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MilestoneDocument $document): bool
    {
        return $user->id === $document->milestone->user_id;
    }

    public function delete(User $user, MilestoneDocument $document): bool
    {
        return $user->id === $document->milestone->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

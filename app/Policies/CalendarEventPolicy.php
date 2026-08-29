<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

/**
 * Policy authorization untuk CalendarEvent.
 * Setiap user hanya bisa akses event miliknya sendiri.
 */
class CalendarEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CalendarEvent $event): bool
    {
        return $user->id === $event->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CalendarEvent $event): bool
    {
        return $user->id === $event->user_id;
    }

    public function delete(User $user, CalendarEvent $event): bool
    {
        return $user->id === $event->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

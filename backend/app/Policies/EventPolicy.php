<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    private function isOwner(User $user, Event $event): bool
    {
        return $user->id === $event->user_id;
    }

    public function view(User $user, Event $event): bool
    {
        return $this->isOwner($user, $event);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->isOwner($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->isOwner($user, $event);
    }
}
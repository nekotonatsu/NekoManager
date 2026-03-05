<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Policies\Traits\OwnershipAuthorization;

class EventPolicy
{
    use OwnershipAuthorization;

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
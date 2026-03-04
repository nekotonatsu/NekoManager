<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Policies\Traits\OwnershipAuthorization;

class TaskPolicy
{
    use OwnershipAuthorization;

    public function view(User $user, Task $task): bool
    {
        return $this->isOwner($user, $task);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->isOwner($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->isOwner($user, $task);
    }
}
<?php

namespace App\Policies\Traits;

use App\Models\User;
// use Illuminate\Auth\Access\HandlesAuthorization;

trait OwnershipAuthorization
{
    // allow denyが追加された際に再度追加
    // use HandlesAuthorization;

    public function before(
        User $user, 
        string $ability, ...$arguments
    ) {
        if ($user->is_admin) {
            return true;
        }

        return null;
    }

    protected function isOwner(User $user, $model): bool
    {
        return (int)$user->id === (int)$model->user_id;
    }
}
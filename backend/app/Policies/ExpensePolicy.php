<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Policies\traits\OwnershipAuthorization;

class ExpensePolicy
{
    use OwnershipAuthorization;

    public function view(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }
}
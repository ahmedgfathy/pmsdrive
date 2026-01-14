<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if the user can update users.
     */
    public function update(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if the user can delete users.
     */
    public function delete(User $user): bool
    {
        return $user->is_admin;
    }
}

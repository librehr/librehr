<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absence $absence): bool
    {
        return in_array($user->role->name, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Absence $absence): bool
    {
        return in_array($user->role->name, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Absence $absence): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Absence $absence): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Absence $absence): bool
    {
        return $user->role->name === 'admin';
    }
}

<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsencePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->getActiveContractId() > 0) {
            return true;
        }

        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absence $absence): bool
    {
        if ($absence->contract_id === $user->getActiveContractId()) {
            return true;
        }

        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->getActiveContractId() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Absence $absence): bool
    {
        return $user->role->name === 'admin';
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

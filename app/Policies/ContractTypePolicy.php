<?php

namespace App\Policies;

use App\Models\ContractType;
use App\Models\User;

class ContractTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContractType $contractType): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContractType $contractType): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContractType $contractType): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContractType $contractType): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContractType $contractType): bool
    {
        return $user->role->name === 'admin';
    }
}

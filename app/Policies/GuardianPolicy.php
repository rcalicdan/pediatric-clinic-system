<?php
// app/Policies/GuardianPolicy.php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardianPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the guardian.
     */
    public function view(User $user, Guardian $guardian): bool
    {
        // Admin and doctor can view any guardian, staff can only view
        return in_array($user->role, ['admin', 'doctor', 'staff']);
    }

    /**
     * Determine whether the user can create guardians.
     */
    public function create(User $user): bool
    {
        // Admin has full control, staff can create guardians
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Determine whether the user can update the guardian.
     */
    public function update(User $user, Guardian $guardian): bool
    {
        // Admin has full control, staff can update guardians
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Determine whether the user can delete the guardian.
     */
    public function delete(User $user, Guardian $guardian): bool
    {
        // Only admin can delete guardians
        return $user->role === 'admin';
    }
}

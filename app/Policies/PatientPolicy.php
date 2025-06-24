<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create patients.
     */
    public function create(User $user): bool
    {
        // Admin has full control, staff can create patients
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Determine whether the user can update the patient.
     */
    public function update(User $user, Patient $patient): bool
    {
        // Admin has full control, staff can update patients
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Determine whether the user can delete the patient.
     */
    public function delete(User $user, Patient $patient): bool
    {
        // Only admin can delete patients
        return $user->role === 'admin';
    }
}

<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use App\Enums\UserRoles;

class ConsultationPolicy
{
    /**
     * Determine whether the user can view any consultations.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRoles::ADMIN->value, UserRoles::DOCTOR->value]);
    }

    /**
     * Determine whether the user can view the consultation.
     */
    public function view(User $user, Consultation $consultation): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Determine whether the user can create consultations.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Determine whether the user can update the consultation.
     */
    public function update(User $user, Consultation $consultation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->role === UserRoles::DOCTOR->value && $consultation->doctor_id === $user->id;
    }

    /**
     * Determine whether the user can delete the consultation.
     */
    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->isAdmin();
    }
}

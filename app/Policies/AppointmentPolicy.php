<?php

namespace App\Policies;

use App\Enums\UserRoles;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRoles::ADMIN, UserRoles::DOCTOR, UserRoles::STAFF]);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [UserRoles::ADMIN, UserRoles::DOCTOR, UserRoles::STAFF]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRoles::ADMIN, UserRoles::DOCTOR, UserRoles::STAFF]);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return in_array($user->role, [UserRoles::ADMIN, UserRoles::DOCTOR, UserRoles::STAFF]);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if (!$appointment->canBeModified()) {
            return false;
        }

        return in_array($user->role, [UserRoles::ADMIN, UserRoles::DOCTOR]);
    }
}

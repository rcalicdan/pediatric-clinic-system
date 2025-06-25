<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Enums\AppointmentStatuses;

class AppointmentObserver
{
    /**
     * Handle the Appointment "creating" event.
     */
    public function creating(Appointment $appointment): void
    {
        // Auto-assign queue number if not provided
        if (!$appointment->queue_number) {
            $appointment->queue_number = Appointment::getNextQueueNumber($appointment->appointment_date);
        }

        // Set default status if not provided
        if (!$appointment->status) {
            $appointment->status = AppointmentStatuses::WAITING;
        }
    }

    /**
     * Handle the Appointment "updating" event.
     */
    public function updating(Appointment $appointment): void
    {
        // Prevent queue number changes on existing appointments
        if ($appointment->isDirty('queue_number') && $appointment->exists) {
            $appointment->queue_number = $appointment->getOriginal('queue_number');
        }

        // Prevent appointment_date changes if appointment is not in waiting status
        if (
            $appointment->isDirty('appointment_date') &&
            $appointment->status !== AppointmentStatuses::WAITING
        ) {
            $appointment->appointment_date = $appointment->getOriginal('appointment_date');
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        // Get the deleted appointment's queue number and date
        $deletedQueueNumber = $appointment->queue_number;
        $appointmentDate = $appointment->appointment_date;

        // Update all appointments on the same date with queue numbers greater than the deleted one
        Appointment::where('appointment_date', $appointmentDate)
            ->where('queue_number', '>', $deletedQueueNumber)
            ->orderBy('queue_number')
            ->get()
            ->each(function ($appointment) {
                // Temporarily disable the observer to prevent infinite loops
                $appointment->withoutEvents(function () use ($appointment) {
                    $appointment->decrement('queue_number');
                });
            });
    }
}

<?php

namespace App\Observers;

use App\Enums\AppointmentStatuses;
use App\Models\Appointment;
use App\Models\Consultation;

class ConsultationObserver
{
    /**
     * Handle the Consultation "created" event.
     */
    public function created(Consultation $consultation): void
    {
        Appointment::where('id', $consultation->appointment_id)
            ->update(['status' => AppointmentStatuses::COMPLETED->value]);
    }

    /**
     * Handle the Consultation "deleted" event.
     */
    public function deleted(Consultation $consultation): void
    {
        Appointment::where('id', $consultation->appointment_id)
            ->update(['status' => AppointmentStatuses::CANCELLED->value]);
    }
}

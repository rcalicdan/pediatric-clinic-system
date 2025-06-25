<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Appointment;
use Carbon\Carbon;

class UniquePatientAppointmentDate implements ValidationRule
{
    protected $patientId;
    protected $ignoreAppointmentId;

    public function __construct($patientId, $ignoreAppointmentId = null)
    {
        $this->patientId = $patientId;
        $this->ignoreAppointmentId = $ignoreAppointmentId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->patientId || !$value) {
            return;
        }

        try {
            $appointmentDate = Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return;
        }

        $query = Appointment::where('patient_id', $this->patientId)
            ->where('appointment_date', $appointmentDate);

        if ($this->ignoreAppointmentId) {
            $query->where('id', '!=', $this->ignoreAppointmentId);
        }

        if ($query->exists()) {
            $fail('This patient already has an appointment on the selected date.');
        }
    }
}

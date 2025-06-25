<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use App\Models\Patient;
use App\Enums\AppointmentStatuses;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Carbon\Carbon;

class UpdatePage extends Component
{
    public Appointment $appointment;
    public $patient_id;
    public $appointment_date;
    public $reason;
    public $notes;
    public $status;

    public function mount(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $this->appointment = $appointment;
        $this->patient_id = $appointment->patient_id;
        $this->appointment_date = $appointment->appointment_date->format('Y-m-d');
        $this->reason = $appointment->reason;
        $this->notes = $appointment->notes;
        $this->status = $appointment->status->value;
    }

    public function rules(): array
    {
        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        // Only allow date changes if appointment is still waiting
        if ($this->appointment->status === AppointmentStatuses::WAITING) {
            $rules['appointment_date'] = ['required', 'date', 'after_or_equal:today'];
        }

        // Only allow status changes based on current status
        $allowedStatuses = $this->appointment->status->getAllowedTransitions();
        if (!empty($allowedStatuses)) {
            $rules['status'] = ['required', Rule::in(array_map(fn($s) => $s->value, $allowedStatuses))];
        }

        return $rules;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('update', $this->appointment);
        $validatedData = $this->validate();

        // Handle status update with validation
        if (isset($validatedData['status'])) {
            $newStatus = AppointmentStatuses::from($validatedData['status']);
            if (!$this->appointment->updateStatus($newStatus)) {
                session()->flash('error', 'Invalid status transition.');
                return;
            }
            unset($validatedData['status']); // Remove from update data since we handled it separately
        }

        $this->appointment->update($validatedData);
        session()->flash('success', 'Appointment updated successfully!');
    }

    public function canUpdateDate(): bool
    {
        return $this->appointment->status === AppointmentStatuses::WAITING;
    }

    public function canUpdateStatus(): bool
    {
        return !empty($this->appointment->status->getAllowedTransitions());
    }

    public function getAvailableStatuses(): array
    {
        return $this->appointment->status->getAllowedTransitions();
    }

    public function render()
    {
        $patients = Patient::orderBy('first_name')->get();
        $availableStatuses = $this->getAvailableStatuses();

        return view('livewire.appointements.update-page', [
            'patients' => $patients,
            'availableStatuses' => $availableStatuses,
            'canUpdateDate' => $this->canUpdateDate(),
            'canUpdateStatus' => $this->canUpdateStatus()
        ]);
    }
}

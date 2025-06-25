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

        if (auth()->user()->isAdmin() || $this->appointment->status === AppointmentStatuses::WAITING) {
            $rules['appointment_date'] = ['required', 'date', 'after_or_equal:today'];
        }

        if (auth()->user()->isAdmin()) {
            $rules['status'] = ['required', Rule::in(array_map(fn($s) => $s->value, AppointmentStatuses::cases()))];
        } else {
            $allowedStatuses = $this->appointment->status->getAllowedTransitions();
            if (!empty($allowedStatuses)) {
                $rules['status'] = ['required', Rule::in(array_map(fn($s) => $s->value, $allowedStatuses))];
            }
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

        try {
            if (isset($validatedData['status'])) {
                $newStatus = AppointmentStatuses::from($validatedData['status']);
                if ($this->appointment->status !== $newStatus) {
                    if (!$this->appointment->updateStatus($newStatus, auth()->user())) {
                        session()->flash('error', 'Invalid status transition.');
                        return;
                    }
                }
                unset($validatedData['status']);
            }

            if (!empty($validatedData)) {
                $this->appointment->update($validatedData);
            }

            session()->flash('success', 'Appointment updated successfully!');
            $this->redirectRoute('appointments.edit', ['appointment' => $this->appointment], navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the appointment.');
        }
    }

    /**
     * Sync component properties with the appointment model
     */
    protected function syncComponentProperties(): void
    {
        $this->patient_id = $this->appointment->patient_id;
        $this->appointment_date = $this->appointment->appointment_date->format('Y-m-d');
        $this->reason = $this->appointment->reason;
        $this->notes = $this->appointment->notes;
        $this->status = $this->appointment->status->value;
    }

    public function canUpdateDate(): bool
    {
        return auth()->user()->isAdmin() || $this->appointment->status === AppointmentStatuses::WAITING;
    }

    public function canUpdateStatus(): bool
    {
        return auth()->user()->isAdmin() || !empty($this->appointment->status->getAllowedTransitions());
    }

    public function getAvailableStatuses(): array
    {
        if (auth()->user()->isAdmin()) {
            return AppointmentStatuses::cases();
        }

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

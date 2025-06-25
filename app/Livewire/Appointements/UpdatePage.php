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

    // Searchable dropdown properties
    public $patientSearch = '';
    public $showPatientDropdown = false;
    public $selectedPatient = null;
    

    public function mount(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $this->appointment = $appointment;
        $this->patient_id = $appointment->patient_id;
        $this->appointment_date = $appointment->appointment_date->format('Y-m-d');
        $this->reason = $appointment->reason;
        $this->notes = $appointment->notes;
        $this->status = $appointment->status->value;
        
        // Set initial patient selection
        $this->selectedPatient = $appointment->patient;
        $this->patientSearch = $appointment->patient->full_name . ' (ID: ' . $appointment->patient->id . ')';
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

    public function updatedPatientSearch()
    {
        $this->showPatientDropdown = !empty($this->patientSearch);
        if (empty($this->patientSearch)) {
            $this->selectedPatient = null;
            $this->patient_id = null;
        }
    }

    public function selectPatient($patientId)
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            $this->patient_id = $patient->id;
            $this->selectedPatient = $patient;
            $this->patientSearch = $patient->full_name . ' (ID: ' . $patient->id . ')';
            $this->showPatientDropdown = false;
        }
    }

    public function clearPatientSelection()
    {
        $this->patient_id = null;
        $this->selectedPatient = null;
        $this->patientSearch = '';
        $this->showPatientDropdown = false;
    }

    public function getSearchedPatientsProperty()
    {
        if (empty($this->patientSearch)) {
            return Patient::orderBy('first_name')
                ->limit(15)
                ->get();
        }

        return Patient::where(function ($query) {
            $query->where('first_name', 'like', '%' . $this->patientSearch . '%')
                ->orWhere('last_name', 'like', '%' . $this->patientSearch . '%')
                ->orWhere('id', 'like', '%' . $this->patientSearch . '%')
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->patientSearch . '%']);
        })
        ->orderBy('first_name')
        ->limit(15)
        ->get();
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
            $patientChanged = isset($validatedData['patient_id']) &&
                $validatedData['patient_id'] != $this->appointment->patient_id;

            $dateChanged = isset($validatedData['appointment_date']) &&
                $validatedData['appointment_date'] !== $this->appointment->appointment_date->format('Y-m-d');

            if ($patientChanged || $dateChanged) {
                $checkPatientId = $validatedData['patient_id'] ?? $this->appointment->patient_id;
                $checkDate = $validatedData['appointment_date'] ?? $this->appointment->appointment_date->format('Y-m-d');

                Appointment::checkPatientAppointmentConflict(
                    $checkPatientId,
                    $checkDate,
                    $this->appointment->id
                );
            }

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
            session()->flash('error', $e->getMessage());
        }
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
        $availableStatuses = $this->getAvailableStatuses();

        return view('livewire.appointements.update-page', [
            'searchedPatients' => $this->searchedPatients,
            'availableStatuses' => $availableStatuses,
            'canUpdateDate' => $this->canUpdateDate(),
            'canUpdateStatus' => $this->canUpdateStatus(),
        ]);
    }
}
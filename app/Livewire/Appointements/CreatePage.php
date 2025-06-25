<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use App\Models\Patient;
use App\Enums\AppointmentStatuses;
use Livewire\Component;
use Carbon\Carbon;

class CreatePage extends Component
{
    public $patient_id;
    public $appointment_date;
    public $reason;
    public $notes;
    
    // Searchable dropdown properties
    public $patientSearch = '';
    public $showPatientDropdown = false;
    public $selectedPatient = null;

    public function mount()
    {
        $this->appointment_date = Carbon::today()->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
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

    public function create()
    {
        $this->authorize('create', Appointment::class);

        $validatedData = $this->validate();

        try {
            Appointment::checkPatientAppointmentConflict(
                $this->patient_id, 
                $this->appointment_date
            );

            $validatedData['status'] = AppointmentStatuses::WAITING->value;
            Appointment::create($validatedData);
            
            session()->flash('success', 'Appointment created successfully.');
            return $this->redirectRoute('appointments.index', navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.appointements.create-page', [
            'searchedPatients' => $this->searchedPatients
        ]);
    }
}
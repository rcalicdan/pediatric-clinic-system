<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Models\Guardian;
use Livewire\Component;
use Illuminate\Validation\Rule;

class UpdatePage extends Component
{
    public Patient $patient;
    public Guardian $guardian;

    // Guardian form fields
    public $guardian_first_name = '';
    public $guardian_last_name = '';
    public $guardian_contact_number = '';
    public $guardian_email = '';
    public $guardian_relationship = '';
    public $guardian_address = '';

    // Patient form fields
    public $patient_first_name = '';
    public $patient_last_name = '';
    public $birth_date = '';
    public $gender = '';

    // Form state
    public $currentStep = 1;

    protected $validationAttributes = [
        'guardian_first_name' => 'guardian first name',
        'guardian_last_name' => 'guardian last name',
        'guardian_contact_number' => 'guardian contact number',
        'guardian_email' => 'guardian email',
        'guardian_relationship' => 'guardian relationship',
        'guardian_address' => 'guardian address',
        'patient_first_name' => 'patient first name',
        'patient_last_name' => 'patient last name',
        'birth_date' => 'birth date',
        'gender' => 'gender',
    ];

    protected function messages()
    {
        return [
            'birth_date.after' => 'Only patients less than 18 years old are allowed.',
        ];
    }

    public function mount(Patient $patient)
    {
        $this->authorize('update', $patient);
        $this->patient = $patient;
        $this->guardian = $patient->guardian;

        // Populate guardian fields
        $this->guardian_first_name = $this->guardian->first_name;
        $this->guardian_last_name = $this->guardian->last_name;
        $this->guardian_contact_number = $this->guardian->contact_number;
        $this->guardian_email = $this->guardian->email;
        $this->guardian_relationship = $this->guardian->relationship;
        $this->guardian_address = $this->guardian->address;

        // Populate patient fields
        $this->patient_first_name = $this->patient->first_name;
        $this->patient_last_name = $this->patient->last_name;
        $this->birth_date = $this->patient->birth_date;
        $this->gender = $this->patient->gender;
    }

    public function getGuardianRules(): array
    {
        return [
            'guardian_first_name' => ['required', 'string', 'min:2', 'max:50'],
            'guardian_last_name' => ['required', 'string', 'min:2', 'max:50'],
            'guardian_contact_number' => ['required', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:50'],
            'guardian_relationship' => ['required', 'string', 'max:50'],
            'guardian_address' => ['required', 'string', 'max:255'],
        ];
    }

    public function getPatientRules(): array
    {
        return [
            'patient_first_name' => ['required', 'string', 'min:2', 'max:50'],
            'patient_last_name' => ['required', 'string', 'min:2', 'max:50'],
            'birth_date' => ['required', 'date', 'before:today', 'after:' . now()->subYears(17)->format('Y-m-d')],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
        ];
    }

    public function updated($propertyName)
    {
        if ($this->currentStep === 1 && str_starts_with($propertyName, 'guardian_')) {
            $this->validateOnly($propertyName, $this->getGuardianRules());
        } elseif ($this->currentStep === 2 && str_starts_with($propertyName, 'patient_')) {
            $this->validateOnly($propertyName, $this->getPatientRules());
        }
    }

    public function updateGuardian()
    {
        $this->authorize('update', $this->guardian);
        $guardianData = $this->validate($this->getGuardianRules());

        // Remove 'guardian_' prefix from keys
        $guardianData = collect($guardianData)->mapWithKeys(function ($value, $key) {
            return [str_replace('guardian_', '', $key) => $value];
        })->toArray();

        $this->guardian->update($guardianData);
        $this->currentStep = 2;

        session()->flash('success', 'Guardian information updated successfully. Now update patient details.');
    }

    public function updatePatient()
    {
        $this->authorize('update', $this->patient);
        $patientData = $this->validate($this->getPatientRules());

        // Remove 'patient_' prefix from keys
        $patientData = collect($patientData)->mapWithKeys(function ($value, $key) {
            return [str_replace('patient_', '', $key) => $value];
        })->toArray();

        $this->patient->update($patientData);

        session()->flash('success', 'Patient updated successfully.');

        return $this->redirectRoute('patients.index', navigate: true);
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function getGenderOptions(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other'
        ];
    }

    public function getRelationshipOptions(): array
    {
        return [
            'parent' => 'Parent',
            'guardian' => 'Guardian',
            'grandparent' => 'Grandparent',
            'sibling' => 'Sibling',
            'relative' => 'Relative',
            'other' => 'Other'
        ];
    }

    public function render()
    {
        return view('livewire.patients.update-page', [
            'genderOptions' => $this->getGenderOptions(),
            'relationshipOptions' => $this->getRelationshipOptions()
        ]);
    }
}

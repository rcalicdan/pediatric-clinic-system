<?php

namespace App\Livewire\Patients;

use App\Models\Guardian;
use App\Models\Patient;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreatePage extends Component
{
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
    public $guardian = null;

    public function mount()
    {
        $this->authorize('create', Patient::class);
    }


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
            'birth_date' => ['required', 'date', 'before:today'],
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

    public function submitGuardian()
    {
        $this->authorize('create', Guardian::class);
        $guardianData = $this->validate($this->getGuardianRules());

        // Remove 'guardian_' prefix from keys
        $guardianData = collect($guardianData)->mapWithKeys(function ($value, $key) {
            return [str_replace('guardian_', '', $key) => $value];
        })->toArray();

        $this->guardian = Guardian::create($guardianData);
        $this->currentStep = 2;

        session()->flash('success', 'Guardian information saved successfully. Now add patient details.');
    }

    public function submitPatient()
    {
        $this->authorize('create', Patient::class);
        $patientData = $this->validate($this->getPatientRules());

        // Remove 'patient_' prefix from keys and add guardian_id
        $patientData = collect($patientData)->mapWithKeys(function ($value, $key) {
            return [str_replace('patient_', '', $key) => $value];
        })->toArray();

        $patientData['guardian_id'] = $this->guardian->id;

        Patient::create($patientData);

        session()->flash('success', 'Patient created successfully.');

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
        return view('livewire.patients.create-page', [
            'genderOptions' => $this->getGenderOptions(),
            'relationshipOptions' => $this->getRelationshipOptions()
        ]);
    }
}

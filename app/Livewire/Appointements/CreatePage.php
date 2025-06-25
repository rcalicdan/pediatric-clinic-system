<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use App\Models\Patient;
use App\Enums\AppointmentStatuses;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Carbon\Carbon;

class CreatePage extends Component
{
    public $patient_id;
    public $appointment_date;
    public $reason;
    public $notes;

    public function mount()
    {
        $this->appointment_date = Carbon::today()->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                Rule::unique('appointments')
                    ->where('patient_id', $this->patient_id)
                    ->where('appointment_date', $this->appointment_date)
            ],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.unique' => 'This patient already has an appointment on the selected date.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->authorize('create', Appointment::class);

        $validatedData = $this->validate();
        $validatedData['status'] = AppointmentStatuses::WAITING->value;

        Appointment::create($validatedData);
        session()->flash('success', 'Appointment created successfully.');

        return $this->redirectRoute('appointments.index', navigate: true);
    }

    public function render()
    {
        $patients = Patient::orderBy('first_name')->get();

        return view('livewire.appointements.create-page', compact('patients'));
    }
}

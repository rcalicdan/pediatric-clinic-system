<?php

namespace App\Livewire\Consultations;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class AppointmentConsultationForm extends Component
{
    public Appointment $appointment;
    public ?Consultation $consultation = null;
    public bool $isEditing = false;
    public bool $showForm = false;
    
    // Form fields
    public $user_id;
    public $diagnosis = '';
    public $treatment = '';
    public $prescription = '';
    public $height_cm = '';
    public $weight_kg = '';
    public $temperature_c = '';
    public $notes = '';
    
    // Available doctors
    public $doctors = [];

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'diagnosis' => 'nullable|string|max:1000',
        'treatment' => 'nullable|string|max:1000',
        'prescription' => 'nullable|string|max:1000',
        'height_cm' => 'nullable|numeric|min:0|max:300',
        'weight_kg' => 'nullable|numeric|min:0|max:500',
        'temperature_c' => 'nullable|numeric|min:30|max:50',
        'notes' => 'nullable|string|max:2000',
    ];

    protected $messages = [
        'user_id.required' => 'Please select a doctor.',
        'user_id.exists' => 'Please select a valid doctor.',
        'height_cm.numeric' => 'Height must be a valid number.',
        'height_cm.min' => 'Height must be greater than 0.',
        'height_cm.max' => 'Height must be less than 300 cm.',
        'weight_kg.numeric' => 'Weight must be a valid number.',
        'weight_kg.min' => 'Weight must be greater than 0.',
        'weight_kg.max' => 'Weight must be less than 500 kg.',
        'temperature_c.numeric' => 'Temperature must be a valid number.',
        'temperature_c.min' => 'Temperature must be at least 30°C.',
        'temperature_c.max' => 'Temperature must be less than 50°C.',
    ];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->consultation = $appointment->consultation;
        $this->isEditing = $this->consultation !== null;
        
        // Load doctors (users with doctor or admin role)
        $this->doctors = User::whereIn('role', ['doctor', 'admin'])
            ->orderBy('first_name')
            ->get();
        
        // Set default doctor to current user if they're a doctor
        if (!$this->isEditing && auth()->user()->isDoctor()) {
            $this->user_id = auth()->id();
        }
        
        // Populate form if editing
        if ($this->isEditing) {
            $this->populateForm();
        }
    }

    public function populateForm()
    {
        if ($this->consultation) {
            $this->user_id = $this->consultation->user_id;
            $this->diagnosis = $this->consultation->diagnosis ?? '';
            $this->treatment = $this->consultation->treatment ?? '';
            $this->prescription = $this->consultation->prescription ?? '';
            $this->height_cm = $this->consultation->height_cm ?? '';
            $this->weight_kg = $this->consultation->weight_kg ?? '';
            $this->temperature_c = $this->consultation->temperature_c ?? '';
            $this->notes = $this->consultation->notes ?? '';
        }
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        if (!$this->isEditing) {
            $this->diagnosis = '';
            $this->treatment = '';
            $this->prescription = '';
            $this->height_cm = '';
            $this->weight_kg = '';
            $this->temperature_c = '';
            $this->notes = '';
            
            if (auth()->user()->isDoctor()) {
                $this->user_id = auth()->id();
            }
        } else {
            $this->populateForm();
        }
        
        $this->resetValidation();
    }

    public function save()
    {
        $this->authorize($this->isEditing ? 'update' : 'create', 
                        $this->isEditing ? $this->consultation : Consultation::class);
        
        $this->validate();

        try {
            $data = [
                'appointment_id' => $this->appointment->id,
                'user_id' => $this->user_id,
                'diagnosis' => $this->diagnosis ?: null,
                'treatment' => $this->treatment ?: null,
                'prescription' => $this->prescription ?: null,
                'height_cm' => $this->height_cm ?: null,
                'weight_kg' => $this->weight_kg ?: null,
                'temperature_c' => $this->temperature_c ?: null,
                'notes' => $this->notes ?: null,
            ];

            if ($this->isEditing) {
                $this->consultation->update($data);
                $message = 'Consultation updated successfully.';
            } else {
                $this->consultation = Consultation::create($data);
                $this->isEditing = true;
                $message = 'Consultation created successfully.';
            }

            $this->showForm = false;
            $this->dispatch('consultation-saved');
            session()->flash('success', $message);
            
            $this->appointment->refresh();
            
        } catch (ValidationException $e) {
            $this->addError('appointment_id', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while saving the consultation.');
        }
    }

    public function delete()
    {
        $this->authorize('delete', $this->consultation);
        
        try {
            $this->consultation->delete();
            $this->consultation = null;
            $this->isEditing = false;
            $this->showForm = false;
            $this->resetForm();
            
            $this->dispatch('consultation-deleted');
            session()->flash('success', 'Consultation deleted successfully.');
            
            // Refresh the appointment relationship
            $this->appointment->refresh();
            
        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while deleting the consultation.');
        }
    }

    public function render()
    {
        return view('livewire.consultations.appointment-consultation-form');
    }
}
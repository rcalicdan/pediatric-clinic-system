<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use Livewire\Component;

class UpdatePage extends Component
{
    public Consultation $consultation;

    public function mount(Consultation $consultation)
    {
        $this->authorize('update', $consultation);
        $this->consultation = $consultation->load(['appointment.patient.guardian', 'doctor']);
    }

    public function render()
    {
        return view('livewire.consultations.update-page');
    }
}

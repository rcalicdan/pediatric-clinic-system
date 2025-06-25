<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use Livewire\Component;

class ViewPage extends Component
{
    public Consultation $consultation;

    public function mount(Consultation $consultation)
    {
        $this->authorize('view', $consultation);
        $this->consultation = $consultation->load(['appointment.patient.guardian', 'doctor']);
    }

    public function render()
    {
        $this->authorize('view', $this->consultation);
        return view('livewire.consultations.view-page');
    }
}

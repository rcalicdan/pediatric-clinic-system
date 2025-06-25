<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use Livewire\Component;

class ViewPage extends Component
{
    public Appointment $appointment;

    public function mount(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $this->appointment = $appointment->load(['patient', 'consultation.doctor', 'invoice']);
    }

    public function render()
    {
        return view('livewire.appointements.view-page');
    }
}
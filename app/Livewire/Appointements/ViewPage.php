<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\Attributes\On;

class ViewPage extends Component
{
    public Appointment $appointment;

    public function mount(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        $this->appointment = $appointment->load(['patient', 'consultation.doctor', 'invoice']);
    }

    #[On('consultation-saved')]
    #[On('consultation-deleted')]
    public function refreshAppointment()
    {
        $this->appointment->refresh();
        $this->appointment->load(['patient', 'consultation.doctor', 'invoice']);
    }

    #[On('consulation-saved')]
    public function flashSessionMessage($event)
    {
        if ($event['status'] === 'success') {
            session()->flash('success', $event['message']);
        }
    }

    public function render()
    {
        return view('livewire.appointements.view-page');
    }
}

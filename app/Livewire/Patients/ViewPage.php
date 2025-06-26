<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Enums\AppointmentStatuses;
use Livewire\Component;
use Livewire\WithPagination;

class ViewPage extends Component
{
    use WithPagination;

    public Patient $patient;

    public function mount(Patient $patient)
    {
        $this->patient = $patient->load(['guardian', 'appointments.consultation',]);
    }

    public function getStatusBadgeClass(AppointmentStatuses $status): string
    {
        return match ($status) {
            AppointmentStatuses::COMPLETED => 'bg-green-100 text-green-800',
            AppointmentStatuses::WAITING => 'bg-blue-100 text-blue-800',
            AppointmentStatuses::CANCELLED => 'bg-red-100 text-red-800',
            AppointmentStatuses::MISSED => 'bg-yellow-100 text-yellow-800',
        };
    }

    public function render()
    {
        $appointments = $this->patient->appointments()
            ->with(['consultation',])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return view('livewire.patients.view-page', compact('appointments'));
    }
}

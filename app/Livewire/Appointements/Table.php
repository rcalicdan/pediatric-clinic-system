<?php

namespace App\Livewire\Appointements;

use App\Models\Appointment;
use App\Models\Patient;
use App\Enums\AppointmentStatuses;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Carbon\Carbon;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'id')]
    public $searchId = '';

    #[Url(as: 'patient')]
    public $searchPatient = '';

    #[Url(as: 'date')]
    public $searchDate = '';

    #[Url(as: 'status')]
    public $searchStatus = '';

    #[Url(as: 'queue')]
    public $searchQueue = '';

    public $isSearchModalOpen = false;
    public $showTodayOnly = true; // Flag to control default filtering

    public function mount()
    {
        // Set default date to today if no search parameters are present
        if (
            empty($this->searchId) &&
            empty($this->searchPatient) &&
            empty($this->searchDate) &&
            empty($this->searchStatus) &&
            empty($this->searchQueue)
        ) {
            $this->searchDate = Carbon::today()->format('Y-m-d');
            $this->showTodayOnly = true;
        } else {
            $this->showTodayOnly = false;
        }
    }

    public function performSearch()
    {
        $this->showTodayOnly = false;
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchPatient = '';
        $this->searchDate = Carbon::today()->format('Y-m-d');
        $this->searchStatus = '';
        $this->searchQueue = '';
        $this->showTodayOnly = true;
        $this->resetPage();
    }

    public function delete(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        if (!$appointment->canBeModified()) {
            session()->flash('error', 'Cannot delete appointment with current status.');
            return;
        }

        $appointment->delete();
        session()->flash('success', 'Appointment deleted successfully.');
    }

    public function updateStatus(Appointment $appointment, $newStatus)
    {
        $this->authorize('update', $appointment);

        $status = AppointmentStatuses::from($newStatus);

        if (!$appointment->updateStatus($status, auth()->user())) {
            session()->flash('error', 'Invalid status transition.');
            return;
        }

        session()->flash('success', 'Appointment status updated successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['patient'])
            ->when($this->searchId, function ($query) {
                return $query->where('id', $this->searchId);
            })
            ->when($this->searchPatient, function ($query) {
                return $query->whereHas('patient', function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->searchPatient}%"]);
                });
            })
            ->when($this->searchDate, function ($query) {
                return $query->whereDate('appointment_date', $this->searchDate);
            })
            ->when($this->searchStatus, function ($query) {
                return $query->where('status', $this->searchStatus);
            })
            ->when($this->searchQueue, function ($query) {
                return $query->where('queue_number', $this->searchQueue);
            })
            ->when($this->showTodayOnly && empty($this->searchDate), function ($query) {
                return $query->whereDate('appointment_date', Carbon::today());
            })
            ->orderBy('appointment_date', 'desc')
            ->orderBy('queue_number')
            ->paginate(20);

        $patients = Patient::orderBy('first_name')->get();
        $availableStatuses = AppointmentStatuses::cases();

        return view('livewire.appointements.table', compact('appointments', 'patients', 'availableStatuses'));
    }
}

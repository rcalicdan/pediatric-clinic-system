<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $searchId = '';
    public $searchPatientName = '';
    public $searchDoctorName = '';
    public $searchDiagnosis = '';
    public $searchDate = '';

    public $isSearchModalOpen = false;

    protected $queryString = [
        'searchId' => ['except' => ''],
        'searchPatientName' => ['except' => ''],
        'searchDoctorName' => ['except' => ''],
        'searchDiagnosis' => ['except' => ''],
        'searchDate' => ['except' => ''],
    ];

    public function updatingSearchId()
    {
        $this->resetPage();
    }

    public function updatingSearchPatientName()
    {
        $this->resetPage();
    }

    public function updatingSearchDoctorName()
    {
        $this->resetPage();
    }

    public function updatingSearchDiagnosis()
    {
        $this->resetPage();
    }

    public function updatingSearchDate()
    {
        $this->resetPage();
    }

    public function search()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchPatientName = '';
        $this->searchDoctorName = '';
        $this->searchDiagnosis = '';
        $this->searchDate = '';
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function render()
    {
        $this->authorize('viewAny', Consultation::class);
        
        $consultations = Consultation::with(['appointment.patient.guardian', 'doctor'])
            ->when($this->searchId, function ($query) {
                $query->where('id', 'like', '%' . $this->searchId . '%');
            })
            ->when($this->searchPatientName, function ($query) {
                $query->whereHas('appointment.patient', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->searchPatientName . '%']);
                });
            })
            ->when($this->searchDoctorName, function ($query) {
                $query->whereHas('doctor', function ($subQuery) {
                    $subQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->searchDoctorName . '%']);
                });
            })
            ->when($this->searchDiagnosis, function ($query) {
                $query->where('diagnosis', 'like', '%' . $this->searchDiagnosis . '%');
            })
            ->when($this->searchDate, function ($query) {
                $query->whereDate('created_at', $this->searchDate);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.consultations.table', [
            'consultations' => $consultations
        ]);
    }
}

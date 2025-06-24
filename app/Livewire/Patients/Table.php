<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'id')]
    public $searchId = '';

    #[Url(as: 'name')]
    public $searchName = '';

    #[Url(as: 'guardian')]
    public $searchGuardian = '';

    #[Url(as: 'birth_date')]
    public $searchBirthDate = '';

    #[Url(as: 'gender')]
    public $searchGender = '';

    public $isSearchModalOpen = false;

    public function performSearch()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchId = '';
        $this->searchName = '';
        $this->searchGuardian = '';
        $this->searchBirthDate = '';
        $this->searchGender = '';
        $this->resetPage();
    }

    public function delete(Patient $patient)
    {
        DB::transaction(function () use ($patient) {
            $guardian = $patient->guardian;
            $patient->delete();

            if ($guardian && $guardian->patients()->count() === 0) {
                $guardian->delete();
            }
        });

        session()->flash('success', 'Patient deleted successfully.');
    }

    public function render()
    {
        $patients = Patient::with('guardian')
            ->when($this->searchId, function ($query) {
                return $query->where('id', $this->searchId);
            })
            ->when($this->searchName, function ($query) {
                return $query->where(function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->searchName}%"]);
                });
            })
            ->when($this->searchGuardian, function ($query) {
                return $query->whereHas('guardian', function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->searchGuardian}%"]);
                });
            })
            ->when($this->searchBirthDate, function ($query) {
                return $query->whereDate('birth_date', $this->searchBirthDate);
            })
            ->when($this->searchGender, function ($query) {
                return $query->where('gender', $this->searchGender);
            })
            ->paginate(20);

        return view('livewire.patients.table', compact('patients'));
    }
}

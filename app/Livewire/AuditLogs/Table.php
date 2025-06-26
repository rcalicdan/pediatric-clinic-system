<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'event')]
    public $searchEvent = '';

    #[Url(as: 'user')]
    public $searchUser = '';

    #[Url(as: 'user_id')]
    public $searchUserId = '';

    #[Url(as: 'type')]
    public $searchType = '';

    #[Url(as: 'date')]
    public $searchDate = '';

    public $isSearchModalOpen = false;

    public function mount()
    {
        // Check authorization
        $this->authorize('viewAny', AuditLog::class);
    }

    public function performSearch()
    {
        $this->resetPage();
        $this->dispatch('search-completed');
    }

    public function clearSearch()
    {
        $this->searchEvent = '';
        $this->searchUser = '';
        $this->searchUserId = '';
        $this->searchType = '';
        $this->searchDate = '';
        $this->resetPage();
    }

    public function getEventBadgeClass(string $event): string
    {
        return match (strtolower($event)) {
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'restored' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        $auditLogs = AuditLog::with('user')
            ->when($this->searchEvent, function ($query) {
                return $query->where('event', 'like', "%{$this->searchEvent}%");
            })
            ->when($this->searchUser, function ($query) {
                return $query->whereHas('user', function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->searchUser}%"])
                      ->orWhere('name', 'like', "%{$this->searchUser}%");
                });
            })
            ->when($this->searchUserId, function ($query) {
                return $query->where('user_id', $this->searchUserId);
            })
            ->when($this->searchType, function ($query) {
                return $query->where('auditable_type', 'like', "%{$this->searchType}%");
            })
            ->when($this->searchDate, function ($query) {
                return $query->whereDate('created_at', $this->searchDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.audit-logs.table', compact('auditLogs'));
    }
}
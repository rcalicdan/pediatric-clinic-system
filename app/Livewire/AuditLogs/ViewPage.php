<?php

namespace App\Livewire\AuditLogs;

use App\Models\AuditLog;
use Livewire\Component;

class ViewPage extends Component
{
    public AuditLog $auditLog;

    public function mount(AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);
        
        $this->auditLog = $auditLog->load('user');
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
        return view('livewire.audit-logs.view-page');
    }
}
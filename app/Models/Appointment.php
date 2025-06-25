<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Enums\AppointmentStatuses;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'queue_number',
        'appointment_date',
        'reason',
        'status',
        'notes'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'status' => AppointmentStatuses::class,
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the next available queue number for a given date
     */
    public static function getNextQueueNumber($date = null)
    {
        $date = $date ?? Carbon::today();

        $lastQueueNumber = self::where('appointment_date', $date)
            ->max('queue_number');

        return ($lastQueueNumber ?? 0) + 1;
    }

    /**
     * Scope to get appointments for a specific date ordered by queue
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date)
            ->orderBy('queue_number');
    }

    /**
     * Scope to get appointments by status
     */
    public function scopeByStatus($query, AppointmentStatuses $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get waiting appointments
     */
    public function scopeWaiting($query)
    {
        return $query->byStatus(AppointmentStatuses::WAITING);
    }

    /**
     * Scope to get in progress appointments
     */
    public function scopeInProgress($query)
    {
        return $query->byStatus(AppointmentStatuses::IN_PROGRESS);
    }

    /**
     * Scope to get completed appointments
     */
    public function scopeCompleted($query)
    {
        return $query->byStatus(AppointmentStatuses::COMPLETED);
    }

    /**
     * Update appointment status with validation
     */
    public function updateStatus(AppointmentStatuses $newStatus): bool
    {
        $allowedTransitions = $this->status->getAllowedTransitions();

        if (!in_array($newStatus, $allowedTransitions)) {
            return false; // Invalid transition
        }

        $this->status = $newStatus;
        return $this->save();
    }

    /**
     * Check if appointment can be modified
     */
    public function canBeModified(): bool
    {
        return !$this->status->isFinalState();
    }

    /**
     * Get the status badge HTML class
     */
    public function getStatusBadgeClass(): string
    {
        return $this->status->getBadgeClass();
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayName(): string
    {
        return $this->status->getDisplayName();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Enums\AppointmentStatuses;
use App\Models\User;

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
     * Admins can bypass state transition validation
     */
    public function updateStatus(AppointmentStatuses $newStatus, ?User $user = null): bool
    {
        if (!$this->status->canTransitionTo($newStatus, $user)) {
            return false;
        }

        $this->status = $newStatus;
        return $this->save();
    }

    /**
     * Check if appointment can be modified
     * Admins can always modify appointments
     */
    public function canBeModified(?User $user = null): bool
    {
        return !$this->status->isFinalState($user);
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

    /**
     * Check if the appointment status can be updated
     * Admins can always update status
     */
    public function canUpdateStatus(?User $user = null): bool
    {
        return $this->canBeModified($user);
    }

    /**
     * Get allowed status transitions for this appointment
     */
    public function getAllowedStatusTransitions(?User $user = null): array
    {
        return $this->status->getAllowedTransitions($user);
    }

    public static function checkPatientAppointmentConflict($patientId, $appointmentDate, $excludeId = null)
    {
        $query = self::where('patient_id', $patientId)
            ->whereDate('appointment_date', $appointmentDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception(
                "This patient already has an appointment scheduled for " . Carbon::parse($appointmentDate)->format('M d, Y')
            );
        }
    }
}

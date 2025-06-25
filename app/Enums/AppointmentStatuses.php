<?php

namespace App\Enums;

enum AppointmentStatuses: string
{
    case WAITING = 'waiting';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case MISSED = 'missed';
    case CANCELLED = 'cancelled';

    public static function getAllStatuses(): array
    {
        return array_map(fn(AppointmentStatuses $status) => $status->value, self::cases());
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::WAITING => 'Waiting',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::MISSED => 'Missed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::WAITING => 'bg-blue-100 text-blue-800',
            self::IN_PROGRESS => 'bg-purple-100 text-purple-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
            self::MISSED => 'bg-yellow-100 text-yellow-800',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::WAITING => 'clock',
            self::IN_PROGRESS => 'play-circle',
            self::COMPLETED => 'check-circle',
            self::MISSED => 'x-circle',
            self::CANCELLED => 'ban',
        };
    }

    /**
     * Get statuses that can be transitioned to from current status
     */
    public function getAllowedTransitions(): array
    {
        return match ($this) {
            self::WAITING => [self::IN_PROGRESS, self::CANCELLED, self::MISSED],
            self::IN_PROGRESS => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED => [],
            self::MISSED => [],
            self::CANCELLED => [],
        };
    }

    /**
     * Check if this status is a final state
     */
    public function isFinalState(): bool
    {
        return in_array($this, [self::COMPLETED, self::MISSED, self::CANCELLED]);
    }
}

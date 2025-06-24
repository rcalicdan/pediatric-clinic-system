<?php

namespace App\Enums;

enum AppointmentStatuses: string
{
    case SCHEDULED = 'scheduled';
    case MISSED = 'missed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public static function getAllStatuses(): array
    {
        return array_map(fn(AppointmentStatuses $status) => $status->value, self::cases());
    }

    public function getDisplayName(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::MISSED => 'Missed',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::SCHEDULED => 'bg-blue-100 text-blue-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
            self::MISSED => 'bg-yellow-100 text-yellow-800',
        };
    }
}

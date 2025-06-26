<?php

namespace App\Services\Dashboard;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\User;
use App\Enums\AppointmentStatuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MySQLDashboardService implements DashboardServiceInterface
{
    public function getTotalPatients(): int
    {
        return Patient::count();
    }

    public function getTotalAppointments(): int
    {
        return Appointment::count();
    }

    public function getPendingAppointments(): int
    {
        return Appointment::where('status', AppointmentStatuses::WAITING)->count();
    }

    public function getCompletedAppointments(): int
    {
        return Appointment::where('status', AppointmentStatuses::COMPLETED)->count();
    }

    public function getTodayAppointments(): int
    {
        return Appointment::whereDate('appointment_date', Carbon::today())->count();
    }

    public function getMonthlyAppointmentsData(): array
    {
        $monthlyData = Appointment::selectRaw('MONTH(appointment_date) as month, COUNT(*) as count')
            ->whereYear('appointment_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categories = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $count = $monthlyData->where('month', $i)->first()->count ?? 0;
            $data[] = $count;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getAppointmentStatusData(): array
    {
        $statusData = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];

        foreach ($statusData as $status) {
            // Use the enum's getDisplayName method
            $statusEnum = AppointmentStatuses::from($status->status);
            $labels[] = $statusEnum->getDisplayName();
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getTopDoctorsData(): array
    {
        $doctorsData = Consultation::select('user_id', DB::raw('COUNT(*) as consultation_count'))
            ->with('doctor:id,first_name,last_name')
            ->groupBy('user_id')
            ->orderByDesc('consultation_count')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($doctorsData as $doctor) {
            $labels[] = $doctor->doctor->full_name ?? 'Unknown';
            $data[] = $doctor->consultation_count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getPatientAgeDistribution(): array
    {
        $ageGroups = Patient::selectRaw('
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN "Under 18"
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30 THEN "18-30"
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50 THEN "31-50"
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END as age_group,
            COUNT(*) as count
        ')
        ->groupBy('age_group')
        ->get();

        $labels = [];
        $data = [];

        foreach ($ageGroups as $group) {
            $labels[] = $group->age_group;
            $data[] = $group->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getAppointmentsByTimeSlot(): array
    {
        $timeSlots = Appointment::selectRaw('
            CASE 
                WHEN HOUR(created_at) BETWEEN 8 AND 11 THEN "Morning (8-11 AM)"
                WHEN HOUR(created_at) BETWEEN 12 AND 15 THEN "Afternoon (12-3 PM)"
                WHEN HOUR(created_at) BETWEEN 16 AND 19 THEN "Evening (4-7 PM)"
                ELSE "Other"
            END as time_slot,
            COUNT(*) as count
        ')
        ->groupBy('time_slot')
        ->get();

        $labels = [];
        $data = [];

        foreach ($timeSlots as $slot) {
            $labels[] = $slot->time_slot;
            $data[] = $slot->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getRecentAppointments(int $limit = 5): array
    {
        return Appointment::with(['patient'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'queue_number' => $appointment->queue_number,
                    'patient_name' => $appointment->patient->full_name,
                    'status' => $appointment->status->value,
                    'status_display' => $appointment->status->getDisplayName(),
                    'status_class' => $appointment->status->getBadgeClass(),
                    'appointment_date' => $appointment->appointment_date->format('M d, Y'),
                    'created_at' => $appointment->created_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    public function getMonthlyPatientsGrowth(): array
    {
        $monthlyData = Patient::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categories = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $count = $monthlyData->where('month', $i)->first()->count ?? 0;
            $data[] = $count;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getConsultationMetrics(): array
    {
        $totalConsultations = Consultation::count();
        $avgConsultationTime = 30; 
        $consultationsToday = Consultation::whereDate('created_at', Carbon::today())->count();
        $consultationsThisWeek = Consultation::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        return [
            'total_consultations' => $totalConsultations,
            'avg_consultation_time' => $avgConsultationTime,
            'consultations_today' => $consultationsToday,
            'consultations_this_week' => $consultationsThisWeek,
        ];
    }
}
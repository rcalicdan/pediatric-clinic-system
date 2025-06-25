<?php

namespace App\Services\Dashboard;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\User;
use App\Enums\AppointmentStatuses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SQLiteDashboardService implements DashboardServiceInterface
{
    public function getTotalPatients(): int
    {
        return DB::connection('sqlite')->table('patients')->count();
    }

    public function getTotalAppointments(): int
    {
        return DB::connection('sqlite')->table('appointments')->count();
    }

    public function getPendingAppointments(): int
    {
        return DB::connection('sqlite')->table('appointments')
            ->where('status', AppointmentStatuses::WAITING->value)
            ->count();
    }

    public function getCompletedAppointments(): int
    {
        return DB::connection('sqlite')->table('appointments')
            ->where('status', AppointmentStatuses::COMPLETED->value)
            ->count();
    }

    public function getTodayAppointments(): int
    {
        return DB::connection('sqlite')->table('appointments')
            ->whereDate('appointment_date', Carbon::today())
            ->count();
    }

    public function getMonthlyRevenue(): float
    {
        $completedAppointments = DB::connection('sqlite')->table('appointments')
            ->where('status', AppointmentStatuses::COMPLETED->value)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return $completedAppointments * 500.00;
    }

    public function getMonthlyAppointmentsData(): array
    {
        $monthlyData = DB::connection('sqlite')->table('appointments')
            ->select(DB::raw('strftime("%m", appointment_date) as month, COUNT(*) as count'))
            ->where(DB::raw('strftime("%Y", appointment_date)'), Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categories = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $count = $monthlyData->where('month', $monthStr)->first()->count ?? 0;
            $data[] = $count;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getAppointmentStatusData(): array
    {
        $statusData = DB::connection('sqlite')->table('appointments')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];

        foreach ($statusData as $status) {
            $labels[] = ucfirst($status->status);
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getRevenueData(): array
    {
        $revenueData = DB::connection('sqlite')->table('appointments')
            ->select(DB::raw('DATE(appointment_date) as date, COUNT(*) * 500 as revenue'))
            ->where('status', AppointmentStatuses::COMPLETED->value)
            ->where('appointment_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $categories = [];
        $data = [];

        foreach ($revenueData as $revenue) {
            $categories[] = Carbon::parse($revenue->date)->format('M d');
            $data[] = $revenue->revenue;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getTopDoctorsData(): array
    {
        $doctorsData = DB::connection('sqlite')->table('consultations')
            ->join('users', 'consultations.user_id', '=', 'users.id')
            ->select('users.id', 'users.first_name', 'users.last_name', DB::raw('COUNT(*) as consultation_count'))
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('consultation_count')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($doctorsData as $doctor) {
            $labels[] = $doctor->first_name . ' ' . $doctor->last_name;
            $data[] = $doctor->consultation_count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getPatientAgeDistribution(): array
    {
        $ageGroups = DB::connection('sqlite')->table('patients')
            ->select(DB::raw('
                CASE 
                    WHEN (julianday("now") - julianday(birth_date)) / 365.25 < 18 THEN "Under 18"
                    WHEN (julianday("now") - julianday(birth_date)) / 365.25 BETWEEN 18 AND 30 THEN "18-30"
                    WHEN (julianday("now") - julianday(birth_date)) / 365.25 BETWEEN 31 AND 50 THEN "31-50"
                    WHEN (julianday("now") - julianday(birth_date)) / 365.25 BETWEEN 51 AND 70 THEN "51-70"
                    ELSE "Over 70"
                END as age_group,
                COUNT(*) as count
            '))
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
        $timeSlots = DB::connection('sqlite')->table('appointments')
            ->select(DB::raw('
                CASE 
                    WHEN CAST(strftime("%H", created_at) AS INTEGER) BETWEEN 8 AND 11 THEN "Morning (8-11 AM)"
                    WHEN CAST(strftime("%H", created_at) AS INTEGER) BETWEEN 12 AND 15 THEN "Afternoon (12-3 PM)"
                    WHEN CAST(strftime("%H", created_at) AS INTEGER) BETWEEN 16 AND 19 THEN "Evening (4-7 PM)"
                    ELSE "Other"
                END as time_slot,
                COUNT(*) as count
            '))
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
        $appointments = DB::connection('sqlite')->table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name'
            )
            ->orderByDesc('appointments.created_at')
            ->limit($limit)
            ->get();

        return $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'queue_number' => $appointment->queue_number,
                'patient_name' => $appointment->first_name . ' ' . $appointment->last_name,
                'status' => $appointment->status,
                'status_display' => ucfirst($appointment->status),
                'status_class' => $this->getStatusClass($appointment->status),
                'appointment_date' => Carbon::parse($appointment->appointment_date)->format('M d, Y'),
                'created_at' => Carbon::parse($appointment->created_at)->format('M d, Y H:i'),
                'amount' => $appointment->status === AppointmentStatuses::COMPLETED->value ? 500.00 : 0,
            ];
        })->toArray();
    }

    public function getMonthlyPatientsGrowth(): array
    {
        $monthlyData = DB::connection('sqlite')->table('patients')
            ->select(DB::raw('strftime("%m", created_at) as month, COUNT(*) as count'))
            ->where(DB::raw('strftime("%Y", created_at)'), Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $categories = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $count = $monthlyData->where('month', $monthStr)->first()->count ?? 0;
            $data[] = $count;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getConsultationMetrics(): array
    {
        $totalConsultations = DB::connection('sqlite')->table('consultations')->count();
        $avgConsultationTime = 30; // minutes - placeholder
        $consultationsToday = DB::connection('sqlite')->table('consultations')
            ->whereDate('created_at', Carbon::today())
            ->count();
        $consultationsThisWeek = DB::connection('sqlite')->table('consultations')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->count();

        return [
            'total_consultations' => $totalConsultations,
            'avg_consultation_time' => $avgConsultationTime,
            'consultations_today' => $consultationsToday,
            'consultations_this_week' => $consultationsThisWeek,
        ];
    }

    private function getStatusClass(string $status): string
    {
        return match ($status) {
            'waiting' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
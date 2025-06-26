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
        $appointments = Appointment::whereYear('appointment_date', Carbon::now()->year)
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->appointment_date)->month;
            });

        $categories = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $data[] = $appointments->get($i)?->count() ?? 0;
        }

        return [
            'categories' => $categories,
            'data' => $data
        ];
    }

    public function getAppointmentStatusData(): array
    {
        $appointments = Appointment::all()->groupBy('status');

        $labels = [];
        $data = [];

        foreach ($appointments as $status => $appointmentGroup) {
            // Handle both enum instances and string values
            if ($status instanceof AppointmentStatuses) {
                $labels[] = $status->getDisplayName();
            } else {
                $statusEnum = AppointmentStatuses::from($status);
                $labels[] = $statusEnum->getDisplayName();
            }
            $data[] = $appointmentGroup->count();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getTopDoctorsData(): array
    {
        $consultations = Consultation::with('doctor:id,first_name,last_name')
            ->get()
            ->groupBy('user_id');

        $doctorsData = [];
        foreach ($consultations as $userId => $userConsultations) {
            $doctor = $userConsultations->first()->doctor;
            $doctorsData[] = [
                'doctor_name' => $doctor ? $doctor->full_name : 'Unknown',
                'consultation_count' => $userConsultations->count()
            ];
        }

        // Sort by consultation count and take top 5
        $topDoctors = collect($doctorsData)
            ->sortByDesc('consultation_count')
            ->take(5);

        $labels = [];
        $data = [];

        foreach ($topDoctors as $doctor) {
            $labels[] = $doctor['doctor_name'];
            $data[] = $doctor['consultation_count'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getPatientAgeDistribution(): array
    {
        $patients = Patient::all();
        $ageGroups = [
            '0-4 years old' => 0,
            '5-9 years old' => 0,
            '10-13 years old' => 0,
            '14-17 years old' => 0
        ];

        foreach ($patients as $patient) {
            $age = Carbon::parse($patient->birth_date)->age;

            if ($age >= 0 && $age <= 4) {
                $ageGroups['0-4 years old']++;
            } elseif ($age >= 5 && $age <= 9) {
                $ageGroups['5-9 years old']++;
            } elseif ($age >= 10 && $age <= 13) {
                $ageGroups['10-13 years old']++;
            } elseif ($age >= 14 && $age <= 17) {
                $ageGroups['14-17 years old']++;
            }
        }

        return [
            'labels' => array_keys($ageGroups),
            'data' => array_values($ageGroups)
        ];
    }

    public function getAppointmentsByTimeSlot(): array
    {
        $appointments = Appointment::all();
        $timeSlots = [
            'Morning (8-11 AM)' => 0,
            'Afternoon (12-3 PM)' => 0,
            'Evening (4-7 PM)' => 0,
            'Other' => 0
        ];

        foreach ($appointments as $appointment) {
            $hour = Carbon::parse($appointment->created_at)->hour;

            if ($hour >= 8 && $hour <= 11) {
                $timeSlots['Morning (8-11 AM)']++;
            } elseif ($hour >= 12 && $hour <= 15) {
                $timeSlots['Afternoon (12-3 PM)']++;
            } elseif ($hour >= 16 && $hour <= 19) {
                $timeSlots['Evening (4-7 PM)']++;
            } else {
                $timeSlots['Other']++;
            }
        }

        return [
            'labels' => array_keys($timeSlots),
            'data' => array_values($timeSlots)
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
                    'appointment_date' => Carbon::parse($appointment->appointment_date)->format('M d, Y'),
                    'created_at' => Carbon::parse($appointment->created_at)->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    public function getMonthlyPatientsGrowth(): array
    {
        $patients = Patient::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($patient) {
                return Carbon::parse($patient->created_at)->month;
            });

        $categories = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $categories[] = Carbon::create()->month($i)->format('M');
            $data[] = $patients->get($i)?->count() ?? 0;
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

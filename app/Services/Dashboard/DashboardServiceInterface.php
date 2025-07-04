<?php

namespace App\Services\Dashboard;

interface DashboardServiceInterface
{
    public function getTotalPatients(): int;
    public function getTotalAppointments(): int;
    public function getPendingAppointments(): int;
    public function getCompletedAppointments(): int;
    public function getTodayAppointments(): int;
    public function getMonthlyAppointmentsData(): array;
    public function getAppointmentStatusData(): array;
    public function getTopDoctorsData(): array;
    public function getPatientAgeDistribution(): array;
    public function getAppointmentsByTimeSlot(): array;
    public function getRecentAppointments(int $limit = 5): array;
    public function getMonthlyPatientsGrowth(): array;
    public function getConsultationMetrics(): array;
}
<?php
// app/Livewire/Dashboard/Page.php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Services\Dashboard\DashboardServiceInterface;

class Page extends Component
{
    protected DashboardServiceInterface $dashboardService;

    public function boot(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function render()
    {
        $data = [
            'totalPatients' => $this->dashboardService->getTotalPatients(),
            'totalAppointments' => $this->dashboardService->getTotalAppointments(),
            'pendingAppointments' => $this->dashboardService->getPendingAppointments(),
            'completedAppointments' => $this->dashboardService->getCompletedAppointments(),
            'todayAppointments' => $this->dashboardService->getTodayAppointments(),
            'monthlyRevenue' => $this->dashboardService->getMonthlyRevenue(),
            'monthlyAppointmentsData' => $this->dashboardService->getMonthlyAppointmentsData(),
            'appointmentStatusData' => $this->dashboardService->getAppointmentStatusData(),
            'revenueData' => $this->dashboardService->getRevenueData(),
            'topDoctorsData' => $this->dashboardService->getTopDoctorsData(),
            'patientAgeDistribution' => $this->dashboardService->getPatientAgeDistribution(),
            'appointmentsByTimeSlot' => $this->dashboardService->getAppointmentsByTimeSlot(),
            'monthlyPatientsGrowth' => $this->dashboardService->getMonthlyPatientsGrowth(),
            'consultationMetrics' => $this->dashboardService->getConsultationMetrics(),
            'recentAppointments' => $this->dashboardService->getRecentAppointments(),
        ];

        return view('livewire.dashboard.page', $data);
    }
}
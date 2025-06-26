<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Enums\AppointmentStatuses;
use Carbon\Carbon;

class UpdateMissedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:update-missed {--dry-run : Preview what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update appointment status to missed for appointments that have passed their scheduled date without consultation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        // Get appointments that are past due and don't have consultations
        $missedAppointments = Appointment::where('appointment_date', '<', Carbon::today())
            ->whereIn('status', [
                AppointmentStatuses::WAITING,
                AppointmentStatuses::IN_PROGRESS
            ])
            ->whereDoesntHave('consultation')
            ->with('patient') // Load patient relationship for better logging
            ->get();

        if ($missedAppointments->isEmpty()) {
            $this->info('No missed appointments found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$missedAppointments->count()} appointments to mark as missed:");
        
        // Display appointments in a table format
        $this->table(
            ['ID', 'Patient ID', 'Queue #', 'Date', 'Current Status', 'Reason'],
            $missedAppointments->map(function ($appointment) {
                return [
                    $appointment->id,
                    $appointment->patient_id,
                    $appointment->queue_number,
                    $appointment->appointment_date->format('Y-m-d'),
                    $appointment->status->getDisplayName(),
                    $appointment->reason ?? 'N/A'
                ];
            })
        );

        if ($isDryRun) {
            $this->info('DRY RUN: The above appointments would be marked as MISSED');
            return Command::SUCCESS;
        }

        // Confirm before proceeding in interactive mode
        if ($this->input->isInteractive()) {
            if (!$this->confirm('Do you want to proceed with updating these appointments to MISSED status?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $successCount = 0;
        $failureCount = 0;
        
        foreach ($missedAppointments as $appointment) {
            try {
                // Use the model's updateStatus method to respect business logic
                if ($appointment->updateStatus(AppointmentStatuses::MISSED)) {
                    $successCount++;
                    $this->line("✓ Updated appointment #{$appointment->id} (Patient ID: {$appointment->patient_id}) to MISSED status");
                } else {
                    $failureCount++;
                    $this->error("✗ Failed to update appointment #{$appointment->id} - Status transition not allowed");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $this->error("✗ Error updating appointment #{$appointment->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Update Summary:");
        $this->info("- Successfully updated: {$successCount} appointments");
        
        if ($failureCount > 0) {
            $this->warn("- Failed to update: {$failureCount} appointments");
        }

        return $failureCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
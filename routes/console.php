<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the missed appointments update task
Schedule::command('appointments:update-missed')
    ->daily()
    ->at('01:00') // Run at 1:00 AM daily
    ->onOneServer() // Prevent running on multiple servers
    ->withoutOverlapping(10) // Prevent overlapping, expire lock after 10 minutes
    ->onSuccess(function () {
        Log::info('Missed appointments update completed successfully');
    })
    ->onFailure(function () {
        Log::error('Missed appointments update failed');
    })
    ->environments(['production', 'staging']); // Only run in specific environments
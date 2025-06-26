<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('appointments:update-missed')
            ->daily()
            ->at('01:00')
            ->onOneServer()
            ->withoutOverlapping(10)
            ->onSuccess(function () {
                Log::info('Missed appointments update completed successfully');
            })
            ->onFailure(function () {
                Log::error('Missed appointments update failed');
            })
            ->environments(['production', 'staging']);
    })->create();

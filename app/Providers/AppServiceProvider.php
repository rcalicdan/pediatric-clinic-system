<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Observers\AppointmentObserver;
use App\Services\Dashboard\DashboardServiceInterface;
use App\Services\Dashboard\MySQLDashboardService;
use App\Services\Dashboard\SQLiteDashboardService;
use App\Models\Consultation;
use App\Models\User;
use App\Observers\ConsultationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardServiceInterface::class, function ($app) {
            $driver = config('dashboard.driver', 'mysql');

            return match ($driver) {
                'sqlite' => new SQLiteDashboardService(),
                'mysql' => new MySQLDashboardService(),
                default => new MySQLDashboardService(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Appointment::observe(AppointmentObserver::class);
        Consultation::observe(ConsultationObserver::class);

        Gate::define('view-dashboard', function (User $user) {
            return $user->isAdmin();
        });
    }
}

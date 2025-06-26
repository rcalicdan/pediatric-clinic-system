<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\User\Table;
use App\Livewire\User\CreatePage;
use App\Livewire\User\UpdatePage;
use App\Livewire\Patients\Table as PatientsTable;
use App\Livewire\Patients\CreatePage as PatientsCreatePage;
use App\Livewire\Patients\UpdatePage as PatientsUpdatePage;
use App\Livewire\Patients\ViewPage as PatientViewPage;
use App\Livewire\Appointements\Table as AppointmentsTable;
use App\Livewire\Appointements\CreatePage as AppointmentsCreatePage;
use App\Livewire\Appointements\ViewPage as AppointmentsViewPage;
use App\Livewire\Appointements\UpdatePage as AppointmentsUpdatePage;
use App\Livewire\Consultations\MyConsultations;
use App\Livewire\Consultations\Table as ConsultationsTable;
use App\Livewire\Consultations\UpdatePage as ConsultationsUpdatePage;
use App\Livewire\Consultations\ViewPage as ConsultationsViewPage;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', App\Livewire\Dashboard\Page::class)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('', Table::class)->name('index');
        Route::get('create', CreatePage::class)->name('create');
        Route::get('{user}/edit', UpdatePage::class)->name('edit');
    });

    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('', PatientsTable::class)->name('index');
        Route::get('create', PatientsCreatePage::class)->name('create');
        Route::get('{patient}', PatientViewPage::class)->name('show');
        Route::get('{patient}/edit', PatientsUpdatePage::class)->name('edit');
    });

    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('', AppointmentsTable::class)->name('index');
        Route::get('create', AppointmentsCreatePage::class)->name('create');
        Route::get('{appointment}', AppointmentsViewPage::class)->name('show');
        Route::get('{appointment}/edit', AppointmentsUpdatePage::class)->name('edit');
    });

    Route::prefix('consultations')->name('consultations.')->group(function () {
        Route::get('', ConsultationsTable::class)->name('index');
        Route::get('{consultation}', ConsultationsViewPage::class)->name('show');
        Route::get('{consultation}/edit', ConsultationsUpdatePage::class)->name('edit');
    });

    Route::get('my-consultations', MyConsultations::class)->name('my-consultations');

    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', App\Livewire\AuditLogs\Table::class)->name('index');
        Route::get('/{auditLog}', App\Livewire\AuditLogs\ViewPage::class)->name('show');
    });

    Route::prefix('settings')->group(function () {
        Route::redirect('', 'profile');
        Route::get('profile', Profile::class)->name('settings.profile');
        Route::get('password', Password::class)->name('settings.password');
        Route::get('appearance', Appearance::class)->name('settings.appearance');
    });
});

require __DIR__ . '/auth.php';

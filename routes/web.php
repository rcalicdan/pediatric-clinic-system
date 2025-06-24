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
        Route::get('{patient}/edit', PatientsUpdatePage::class)->name('edit');
    });

    Route::prefix('settings')->group(function () {
        Route::redirect('', 'profile');
        Route::get('profile', Profile::class)->name('settings.profile');
        Route::get('password', Password::class)->name('settings.password');
        Route::get('appearance', Appearance::class)->name('settings.appearance');
    });
});

require __DIR__ . '/auth.php';

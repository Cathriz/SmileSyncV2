<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;



Route::get('/', function () {
    return view('landingpage');
});

Route::get('/home', function () {
    return view('landingpage');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// Login Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Forgot password page
Route::get('/forget', function () {
    return view('forget');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ✅ Appointment routes
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update'); // ✅ Add this
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy'); // ✅ Add this
});
// ✅ Record routes

Route::middleware('auth')->group(function () {
    Route::get('/records', [RecordController::class, 'index'])->name('records');
    Route::post('/records/update', [RecordController::class, 'update']);
    Route::delete('/records/{id}', [RecordController::class, 'delete'])->name('records.delete');
});

// Report routes
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
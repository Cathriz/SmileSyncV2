<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PatientController;

Route::get('/db-check', function () {
    $db = DB::select("SELECT DATABASE() AS db");
    return $db[0]->db;
});

// --- PUBLIC ROUTES (Landing, Auth) ---
Route::get('/', function () {
    return view('landingpage');
});
Route::get('/home', function () {
    return view('landingpage');
});

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forget', function () {
    return view('forget');
});
// Logout (Must be protected)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// --- PROTECTED ROUTES (Requires 'auth' middleware) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patient Profile Update (uses POST)
    Route::post('/patient/update_profile', [PatientController::class, 'updateProfile'])->name('patients.update_profile');

    // APPOINTMENT Routes (RESTful)
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // RECORD Routes (Cleaned up)
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::post('/records/add', [RecordController::class, 'add'])->name('records.add');
    
    // Use PUT for updating a record
    Route::put('/records/update', [RecordController::class, 'update'])->name('records.update'); 
    
    // Use DELETE for deleting a record
    Route::delete('/records/delete/{id}', [RecordController::class, 'delete'])->name('records.delete');
    
    // Patient-specific records view
    Route::get('/records/patient/{patient_name}', [RecordController::class, 'showPatient'])->name('records.show_patient');

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Doctor Routes (Resource)
    Route::resource('doctors', DoctorController::class);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
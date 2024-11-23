<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController, MajorController, NClinicsController,
    DoctorController, PatientController, AppointmentController,
    DiseaseController, ReservationController, InvoiceController,MedicalNewsController
};

// Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update-password', [UserController::class, 'updatePassword']);
    
    // Resources

});   
 Route::apiResources([
        'users' => UserController::class,
        'majors' => MajorController::class,
        'clinics' => NClinicsController::class,
        'doctors' => DoctorController::class,
        'patients' => PatientController::class,
        'appointments' => AppointmentController::class,
        'diseases' => DiseaseController::class,
        'reservations' => ReservationController::class,
        'invoices' => InvoiceController::class,
        'medical-news' => MedicalNewsController::class,
    ]);
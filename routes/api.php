<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController, MajorController, NClinicsController,
    DoctorController, PatientController, PatientArchiveController,
    DiseaseController, InvoiceController, MedicalNewsController
};
use App\Http\Controllers\ReservationController;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update-password', [UserController::class, 'updatePassword']);
});

Route::middleware('api')->group(function () {    
    Route::get('/reservations-slots', [ReservationController::class, 'getAvailableSlots']);
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::get('/reservations/search', [ReservationController::class, 'search']);
});
Route::middleware(['auth'])->group(function () {
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])
         ->name('reservations.confirm');
    Route::post('reservations/{reservation}/reject', [ReservationController::class, 'reject'])
         ->name('reservations.reject');
});
Route::post('/clinic/register', [ClinicAuthController::class, 'register']);

Route::apiResources([
    'users' => UserController::class,
    'majors' => MajorController::class,
    'clinics' => NClinicsController::class,
    'doctors' => DoctorController::class,
    'patients' => PatientController::class,
    'patientarchive' => PatientArchiveController::class,
    'reservations' => ReservationController::class,  
    'diseases' => DiseaseController::class,
    'invoices' => InvoiceController::class,
    'medical-news' => MedicalNewsController::class,
]);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController, MajorController, NClinicsController,
    DoctorController, PatientController, PatientArchiveController,
    DiseaseController, ReservationController, InvoiceController, MedicalNewsController
};

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update-password', [UserController::class, 'updatePassword']);
});

Route::middleware('api')->group(function () {    
    Route::get('/doctors/id/{id}', [DoctorController::class, 'show1']);
    Route::get('/reservations-slots', [ReservationController::class, 'getAvailableSlots']);
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::get('/reservations/search', [ReservationController::class, 'search']);
    Route::put('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);

});

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

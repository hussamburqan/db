<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController, MajorController, NClinicsController,
    DoctorController, PatientController, PatientArchiveController,
    DiseaseController, ReservationController, InvoiceController,MedicalNewsController
};

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update-password', [UserController::class, 'updatePassword']);
    
 

});  


Route::middleware('api')->group(function () {    

    Route::get('/reservations-slots', [ReservationController::class, 'getAvailableSlots']);
    Route::apiResource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
});
 Route::apiResources([
        'users' => UserController::class,
        'majors' => MajorController::class,
        'clinics' => NClinicsController::class,
        'doctors' => DoctorController::class,
        'patients' => PatientController::class,
        'patientarchive' => PatientArchiveController::class,
        'diseases' => DiseaseController::class,
        'invoices' => InvoiceController::class,
        'medical-news' => MedicalNewsController::class,
    ]);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\NClinicController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReservationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('add',function(){
  return "OK";
});
// Appointment
Route::prefix('appointments')->group(function () {
    Route::post('add', [AppointmentController::class, 'add']);
    Route::get('all', [AppointmentController::class, 'all']);
    Route::delete('{id}', [AppointmentController::class, 'delete']);
    Route::put('edit/{id}', [AppointmentController::class, 'update']);
});

// User
Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::get('all', [UserController::class, 'all']);
    Route::delete('{id}', [UserController::class, 'delete']);
    Route::put('edit/{id}', [UserController::class, 'update']);
});

// Doctor
Route::prefix('doctors')->group(function () {
    Route::post('add', [DoctorController::class, 'add']);
    Route::get('all', [DoctorController::class, 'all']);
    Route::get('one', [DoctorController::class, 'one']);
    Route::get('one/{id}', [DoctorController::class, 'one2']);
    Route::delete('{id}', [DoctorController::class, 'delete']);
    Route::put('edit/{id}', [DoctorController::class, 'update']);
});

// Patient
Route::prefix('patients')->group(function () {
    Route::post('add', [PatientController::class, 'add']);
    Route::get('all', [PatientController::class, 'all']);
    Route::delete('{id}', [PatientController::class, 'delete']);
    Route::put('edit/{id}', [PatientController::class, 'update']);
});

// Invoice
Route::prefix('invoices')->group(function () {
    Route::post('add', [InvoiceController::class, 'add']);
    Route::get('all', [InvoiceController::class, 'all']);
    Route::delete('{id}', [InvoiceController::class, 'delete']);
    Route::put('edit/{id}', [InvoiceController::class, 'update']);
});

// Major
Route::prefix('majors')->group(function () {
    Route::post('add', [MajorController::class, 'add']);
    Route::get('all', [MajorController::class, 'all']);
    Route::delete('{id}', [MajorController::class, 'delete']);
    Route::put('edit/{id}', [MajorController::class, 'update']);
});

// Medication
Route::prefix('medications')->group(function () {
    Route::post('add', [MedicationController::class, 'add']);
    Route::get('all', [MedicationController::class, 'all']);
    Route::delete('{id}', [MedicationController::class, 'delete']);
    Route::put('edit/{id}', [MedicationController::class, 'update']);
});

// NClinic
Route::prefix('nclinics')->group(function () {
    Route::post('add', [NClinicController::class, 'add']);
    Route::get('all', [NClinicController::class, 'all']);
    Route::delete('{id}', [NClinicController::class, 'delete']);
    Route::put('edit/{id}', [NClinicController::class, 'update']);
});

// Prescription
Route::prefix('prescriptions')->group(function () {
    Route::post('add', [PrescriptionController::class, 'add']);
    Route::get('all', [PrescriptionController::class, 'all']);
    Route::delete('{id}', [PrescriptionController::class, 'delete']);
    Route::put('edit/{id}', [PrescriptionController::class, 'update']);
});

// Reservation
Route::prefix('reservations')->group(function () {
    Route::post('add', [ReservationController::class, 'add']);
    Route::get('all', [ReservationController::class, 'all']);
    Route::delete('{id}', [ReservationController::class, 'delete']);
    Route::put('edit/{id}', [ReservationController::class, 'update']);
});
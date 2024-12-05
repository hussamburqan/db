<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\NClinicsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
   Route::get('/clinic/register', [NClinicsController::class, 'showRegisterForm'])
        ->name('clinic.register.form');
   Route::post('/clinic/register', [NClinicsController::class, 'register'])
        ->name('clinic.register');
   Route::get('/clinic/login', [NClinicsController::class, 'showLoginForm'])
        ->name('clinic.login.form');
   Route::post('/clinic/login', [NClinicsController::class, 'login'])
        ->name('clinic.login');
});

Route::middleware(['auth'])->group(function () {
   Route::get('/clinic/dashboard', [NClinicsController::class, 'dashboard'])
        ->name('clinic.dashboard');
        
   Route::post('/logout', function () {
       Auth::logout();
       return redirect()->route('clinic.login.form')
                       ->with('success', 'تم تسجيل الخروج بنجاح');
   })->name('logout');

   Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])
        ->name('reservations.confirm');
   Route::post('reservations/{reservation}/reject', [ReservationController::class, 'reject'])
        ->name('reservations.reject');

   Route::resource('doctors', DoctorController::class);
   Route::get('doctors/{doctor}', [DoctorController::class, 'show'])
        ->name('doctors.show');

   Route::get('clinic/profile', [NClinicsController::class, 'editProfile'])
        ->name('clinic.profile.edit');
   Route::put('clinic/profile', [NClinicsController::class, 'updateProfile'])
        ->name('clinic.profile.update');

   Route::get('invoices/create', [InvoiceController::class, 'create'])
        ->name('invoices.create');

   Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');

});

Route::get('/', function () {
   return view('welcome');
});
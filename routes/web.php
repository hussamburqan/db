<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\NClinicsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/clinic/login');

Route::get('/login', function() {
    return redirect('/clinic/login');
})->name('login');

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
Route::fallback(function () {
     return redirect()->route('clinic.login.form');
 });
Route::middleware(['auth'])->group(function () {
    Route::get('/clinic/dashboard', [NClinicsController::class, 'dashboard'])
        ->name('clinic.dashboard');
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('clinic.login.form')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    })->name('logout');

    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::post('{reservation}/confirm', [ReservationController::class, 'confirm'])
            ->name('confirm');
        Route::post('{reservation}/reject', [ReservationController::class, 'reject'])
            ->name('reject');
    });
    Route::put('reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);
    
    Route::resource('doctors', DoctorController::class);

    Route::prefix('clinic/profile')->name('clinic.profile.')->group(function () {
        Route::get('/', [NClinicsController::class, 'editProfile'])
            ->name('edit');
        Route::put('/', [NClinicsController::class, 'updateProfile'])
            ->name('update');
    });
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/details/{invoice}', function (Invoice $invoice) {
            return view('invoices.details', compact('invoice'));
        })->name('details');
    });
});
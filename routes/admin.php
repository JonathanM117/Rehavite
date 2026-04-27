<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ControlCenterController;

Route::middleware(['auth'])->group(function () {
    Route::get('', [HomeController::class, 'index'])->name('admin.home');

    Route::get('control-center', [ControlCenterController::class, 'index'])->name('admin.control-center');

    Route::resource('users', UserController::class)->names('admin.users');
    Route::get('profile', [UserController::class, 'profile'])->name('admin.users.profile');
    Route::resource('patients', PatientController::class)->names('admin.patients');
    Route::resource('medical-records', MedicalRecordController::class)
        ->names('admin.medical-records')
        ->only(['store', 'update', 'destroy']);
    Route::resource('consultations', ConsultationController::class)
        ->names('admin.consultations')
        ->only(['store', 'update', 'destroy']);
    Route::resource('payments', PaymentController::class)
        ->names('admin.payments')
        ->only(['store', 'update', 'destroy']);
});
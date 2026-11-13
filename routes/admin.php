<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ControlCenterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ExerciseBankController;

Route::middleware(['auth'])->group(function () {
    Route::get('', [HomeController::class, 'index'])->name('admin.home');
    Route::post('log-click', [\App\Http\Controllers\Admin\TelemetryController::class, 'logClick'])->name('admin.log-click');

    Route::get('centro-control', [ControlCenterController::class, 'index'])->name('admin.control-center');

    Route::resource('usuarios', UserController::class)
        ->names('admin.users')
        ->parameters(['usuarios' => 'user']);
    Route::get('perfil', [UserController::class, 'profile'])->name('admin.users.profile');

    // Deletion workflow
    Route::post('pacientes/{patient}/solicitar-eliminacion',  [PatientController::class, 'requestDeletion'])->name('admin.patients.request-deletion');
    Route::post('pacientes/{patient}/eliminacion/confirmar',  [PatientController::class, 'confirmDeletion'])->name('admin.patients.deletion.confirm');
    Route::post('pacientes/{patient}/eliminacion/rechazar',   [PatientController::class, 'rejectDeletion'])->name('admin.patients.deletion.reject');
    Route::post('pacientes/{patient}/eliminacion/archivar',   [PatientController::class, 'archivePatient'])->name('admin.patients.deletion.archive');
    Route::get('pacientes/archivados',                        [PatientController::class, 'archived'])->name('admin.patients.archived');

    // Trash workflow
    Route::get('pacientes/papelera',                          [PatientController::class, 'trashed'])->name('admin.patients.trashed');
    Route::post('pacientes/{id}/restaurar',                   [PatientController::class, 'restoreTrashed'])->name('admin.patients.restore');
    Route::delete('pacientes/{id}/eliminar-permanente',       [PatientController::class, 'forceDestroy'])->name('admin.patients.force-delete');

    // Reports
    Route::get('pacientes/{patient}/reporte/pdf',             [ReportController::class, 'patientPdf'])->name('admin.reports.patient.pdf');
    Route::get('pacientes/{patient}/reporte/excel',           [ReportController::class, 'patientExcel'])->name('admin.reports.patient.excel');

    // Exercise bank (standalone tool, driven by a file uploaded from the Control Center)
    Route::get('centro-control/banco-ejercicios/preview', [ExerciseBankController::class, 'preview'])->name('admin.exercise-bank.preview');
    Route::get('pacientes/{patient}/plan-ejercicios',      [ExerciseBankController::class, 'show'])->name('admin.patients.exercise-plan');

    Route::resource('pacientes', PatientController::class)
        ->names('admin.patients')
        ->parameters(['pacientes' => 'patient'])
        ->except(['edit']);
    Route::resource('consultas', ConsultationController::class)
        ->names('admin.consultations')
        ->parameters(['consultas' => 'consultation'])
        ->only(['store', 'update', 'destroy']);
    Route::get('consultas/{consultation}', \App\Http\Livewire\ConsultationDetail::class)->name('admin.consultations.show');
    Route::get('pagos', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::resource('pagos', PaymentController::class)
        ->names('admin.payments')
        ->parameters(['pagos' => 'payment'])
        ->only(['store', 'update', 'destroy']);
    Route::post('pagos/{payment}/abonos', [PaymentController::class, 'storeInstallment'])->name('admin.payments.installments.store');
    
    Route::get('agenda', \App\Http\Livewire\AgendaManager::class)->name('admin.agenda');
    
    Route::get('ping', function() {
        return response()->json(['status' => 'ok']);
    })->name('admin.ping');
});
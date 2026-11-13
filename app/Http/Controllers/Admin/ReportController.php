<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Exports\PatientExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Download patient PDF report.
     */
    public function patientPdf(Patient $patient)
    {
        $this->authorizePatient($patient);

        $patient->load([
            'user',
            'consultations.evolutionNotes',
            'consultations.exercises',
            'consultations.studies',
            'payments',
        ]);

        $logoPath = resource_path('LogoRehavite.jpeg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        $pdf = Pdf::loadView('pdf.patient_report', [
            'patient'       => $patient,
            'consultations' => $patient->consultations,
            'logoBase64'    => $logoBase64,
        ])->setPaper('a4', 'portrait');

        $filename = 'Expediente_' . $patient->expediente_id . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download patient Excel report.
     */
    public function patientExcel(Patient $patient)
    {
        $this->authorizePatient($patient);

        $filename = 'Expediente_' . $patient->expediente_id . '_' . now()->format('Ymd') . '.xlsx';

        return (new PatientExport($patient))->download($filename);
    }

    /**
     * Check access (admin, owner, or collaborator).
     */
    protected function authorizePatient(Patient $patient): void
    {
        abort_unless($patient->canBeAccessedBy(auth()->user()), 403);
    }
}

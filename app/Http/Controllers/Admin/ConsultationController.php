<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'reason' => ['required', 'string', 'max:2000'],
            'current_condition' => ['nullable', 'array'],
            'previous_studies_notes' => ['nullable', 'string', 'max:2000'],
            'physical_exam' => ['nullable', 'array'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'prognosis' => ['nullable', 'string', 'max:2000'],
            'treatment_objectives' => ['nullable', 'string', 'max:2000'],
            'discharge_recommendations' => ['nullable', 'string', 'max:2000'],
            'discharge_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['user_id'] = auth()->id();
        $consultation = Consultation::create($validated);

        AuditLog::record('created', $consultation, 'Creó nueva consulta: ' . Str::limit($validated['reason'], 50));

        return redirect()
            ->route('admin.patients.show', ['patient' => $validated['patient_id'], 'activeTab' => 'consultas'])
            ->with('success', 'Consulta registrada con éxito');
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
            'current_condition' => ['nullable', 'array'],
            'previous_studies_notes' => ['nullable', 'string', 'max:2000'],
            'physical_exam' => ['nullable', 'array'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'prognosis' => ['nullable', 'string', 'max:2000'],
            'treatment_objectives' => ['nullable', 'string', 'max:2000'],
            'discharge_recommendations' => ['nullable', 'string', 'max:2000'],
            'discharge_reason' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
        ]);

        $consultation->update($validated);

        AuditLog::record('updated', $consultation, 'Actualizó consulta');

        return redirect()
            ->route('admin.patients.show', ['patient' => $consultation->patient_id, 'activeTab' => 'consultas'])
            ->with('success', 'Consulta actualizada con éxito');
    }

    public function destroy(Consultation $consultation)
    {
        $patientId = $consultation->patient_id;
        $consultation->delete();

        return redirect()
            ->route('admin.patients.show', ['patient' => $patientId, 'activeTab' => 'consultas'])
            ->with('success', 'Consulta eliminada con éxito');
    }
}

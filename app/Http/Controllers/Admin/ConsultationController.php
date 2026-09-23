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
    /**
     * Capitaliza la primera letra de cada oración en un string.
     */
    private function capitalize(?string $value): ?string
    {
        if (is_null($value) || trim($value) === '') return $value;
        $value = trim($value);
        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1);
    }

    /**
     * Capitaliza todos los strings de un array asociativo (un nivel).
     */
    private function capitalizeArray(?array $arr): ?array
    {
        if (is_null($arr)) return null;
        return array_map(fn($v) => is_string($v) ? $this->capitalize($v) : $v, $arr);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'               => ['required', 'exists:patients,id'],
            'reason'                   => ['required', 'string', 'max:2000'],
            'current_condition'        => ['nullable', 'array'],
            'previous_studies_notes'   => ['nullable', 'string', 'max:2000'],
            'physical_exam'            => ['nullable', 'array'],
            'diagnosis'                => ['nullable', 'string', 'max:2000'],
            'prognosis'                => ['nullable', 'string', 'max:2000'],
            'treatment_objectives'     => ['nullable', 'string', 'max:2000'],
            'discharge_recommendations'=> ['nullable', 'string', 'max:2000'],
            'discharge_reason'         => ['nullable', 'string', 'max:2000'],
        ]);

        // Capitalize text fields before saving
        $validated['reason']                   = $this->capitalize($validated['reason'] ?? null);
        $validated['diagnosis']                = $this->capitalize($validated['diagnosis'] ?? null);
        $validated['prognosis']                = $this->capitalize($validated['prognosis'] ?? null);
        $validated['treatment_objectives']     = $this->capitalize($validated['treatment_objectives'] ?? null);
        $validated['previous_studies_notes']   = $this->capitalize($validated['previous_studies_notes'] ?? null);
        $validated['discharge_recommendations']= $this->capitalize($validated['discharge_recommendations'] ?? null);
        $validated['discharge_reason']         = $this->capitalize($validated['discharge_reason'] ?? null);
        $validated['current_condition']        = $this->capitalizeArray($validated['current_condition'] ?? null);
        $validated['physical_exam']            = $this->capitalizeArray($validated['physical_exam'] ?? null);

        $validated['user_id'] = auth()->id();
        $consultation = Consultation::create($validated);

        AuditLog::record('created', $consultation, 'Creó nueva consulta: ' . Str::limit($validated['reason'], 50));

        return redirect()
            ->route('admin.consultations.show', $consultation->id)
            ->with('success', 'Consulta registrada con éxito. Puedes completar los detalles aquí.');
    }

    public function update(Request $request, Consultation $consultation)
    {
        // Enforce 48-hour immutability for non-admin users
        if (!auth()->user()->admin) {
            $createdAt = $consultation->created_at;
            if ($createdAt && $createdAt->diffInHours(now()) > 48) {
                return redirect()
                    ->route('admin.consultations.show', $consultation->id)
                    ->with('error', 'La consulta no puede modificarse después de 48 horas. Contacta al administrador.');
            }
        }

        $validated = $request->validate([
            'reason'                   => ['required', 'string', 'max:2000'],
            'current_condition'        => ['nullable', 'array'],
            'previous_studies_notes'   => ['nullable', 'string', 'max:2000'],
            'physical_exam'            => ['nullable', 'array'],
            'diagnosis'                => ['nullable', 'string', 'max:2000'],
            'prognosis'                => ['nullable', 'string', 'max:2000'],
            'treatment_objectives'     => ['nullable', 'string', 'max:2000'],
            'discharge_recommendations'=> ['nullable', 'string', 'max:2000'],
            'discharge_reason'         => ['nullable', 'string', 'max:2000'],
            'status'                   => ['nullable', 'in:valoracion,activo,seguimiento,alta_clinica,suspendido,abandono,referido,inactivo,open,in_progress,closed'],
        ]);

        // Capitalize text fields before saving
        $validated['reason']                   = $this->capitalize($validated['reason'] ?? null);
        $validated['diagnosis']                = $this->capitalize($validated['diagnosis'] ?? null);
        $validated['prognosis']                = $this->capitalize($validated['prognosis'] ?? null);
        $validated['treatment_objectives']     = $this->capitalize($validated['treatment_objectives'] ?? null);
        $validated['previous_studies_notes']   = $this->capitalize($validated['previous_studies_notes'] ?? null);
        $validated['discharge_recommendations']= $this->capitalize($validated['discharge_recommendations'] ?? null);
        $validated['discharge_reason']         = $this->capitalize($validated['discharge_reason'] ?? null);
        $validated['current_condition']        = $this->capitalizeArray($validated['current_condition'] ?? null);
        $validated['physical_exam']            = $this->capitalizeArray($validated['physical_exam'] ?? null);

        $consultation->update($validated);

        AuditLog::record('updated', $consultation, 'Actualizó consulta');

        return redirect()
            ->route('admin.consultations.show', $consultation->id)
            ->with('success', 'Consulta actualizada con éxito');
    }

    public function destroy(Consultation $consultation)
    {
        $patientId = $consultation->patient_id;

        // Enforce 48-hour immutability for non-admin users
        if (!auth()->user()->admin) {
            $createdAt = $consultation->created_at;
            if ($createdAt && $createdAt->diffInHours(now()) > 48) {
                return redirect()
                    ->route('admin.patients.show', ['patient' => $patientId, 'activeTab' => 'consultas'])
                    ->with('error', 'La consulta no puede eliminarse después de 48 horas. Contacta al administrador.');
            }
        }

        $consultation->delete();

        return redirect()
            ->route('admin.patients.show', ['patient' => $patientId, 'activeTab' => 'consultas'])
            ->with('success', 'Consulta eliminada con éxito');
    }
}

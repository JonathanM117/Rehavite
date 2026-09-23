<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    private function cap(?string $v): ?string
    {
        return $v;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'                  => ['required', 'exists:patients,id'],
            'age'                         => ['nullable', 'integer', 'min:0', 'max:130'],
            'marital_status'              => ['nullable', 'string', 'max:50'],
            'address'                     => ['nullable', 'string', 'max:500'],
            'laterality'                  => ['nullable', 'string', 'max:50'],
            'occupation'                  => ['nullable', 'string', 'max:255'],
            'economic_dependency'         => ['nullable', 'string', 'max:255'],
            'primary_caregiver'           => ['nullable', 'string', 'max:255'],
            'guardian_name'               => ['nullable', 'string', 'max:255'],
            'guardian_birth_date'         => ['nullable', 'date'],
            'guardian_age'                => ['nullable', 'integer', 'min:0'],
            'guardian_gender'             => ['nullable', 'string', 'max:50'],
            'observations'                => ['nullable', 'string', 'max:2000'],
            'family_history'              => ['nullable', 'array'],
            'personal_non_pathological'   => ['nullable', 'array'],
            'personal_pathological'       => ['nullable', 'array'],
            'systems_review'              => ['nullable', 'array'],
            'pediatric_history'           => ['nullable', 'array'],
        ]);

        // Capitalize text fields
        foreach (['marital_status', 'address', 'laterality', 'occupation', 'economic_dependency', 'primary_caregiver', 'guardian_name', 'guardian_gender', 'observations'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $this->cap($validated[$field]);
            }
        }

        $record = MedicalRecord::create($validated);

        AuditLog::record('created', $record, 'Historia clínica registrada');

        return redirect()
            ->route('admin.patients.show', $validated['patient_id'])
            ->with('success', 'Historia clínica registrada con éxito');
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'age'                         => ['nullable', 'integer', 'min:0', 'max:130'],
            'marital_status'              => ['nullable', 'string', 'max:50'],
            'address'                     => ['nullable', 'string', 'max:500'],
            'laterality'                  => ['nullable', 'string', 'max:50'],
            'occupation'                  => ['nullable', 'string', 'max:255'],
            'economic_dependency'         => ['nullable', 'string', 'max:255'],
            'primary_caregiver'           => ['nullable', 'string', 'max:255'],
            'guardian_name'               => ['nullable', 'string', 'max:255'],
            'guardian_birth_date'         => ['nullable', 'date'],
            'guardian_age'                => ['nullable', 'integer', 'min:0'],
            'guardian_gender'             => ['nullable', 'string', 'max:50'],
            'observations'                => ['nullable', 'string', 'max:2000'],
            'family_history'              => ['nullable', 'array'],
            'personal_non_pathological'   => ['nullable', 'array'],
            'personal_pathological'       => ['nullable', 'array'],
            'systems_review'              => ['nullable', 'array'],
            'pediatric_history'           => ['nullable', 'array'],
        ]);

        // Capitalize text fields
        foreach (['marital_status', 'address', 'laterality', 'occupation', 'economic_dependency', 'primary_caregiver', 'guardian_name', 'guardian_gender', 'observations'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $this->cap($validated[$field]);
            }
        }



        $medicalRecord->update($validated);

        AuditLog::record('updated', $medicalRecord, 'Historia clínica actualizada por ' . auth()->user()->name);

        return redirect()
            ->route('admin.patients.show', $medicalRecord->patient_id)
            ->with('success', 'Historia clínica actualizada con éxito');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $patientId = $medicalRecord->patient_id;
        $medicalRecord->delete();

        return redirect()
            ->route('admin.patients.show', $patientId)
            ->with('success', 'Historia clínica eliminada con éxito');
    }
}

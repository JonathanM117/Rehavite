<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'laterality' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'economic_dependency' => ['nullable', 'string', 'max:255'],
            'primary_caregiver' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_birth_date' => ['nullable', 'date'],
            'guardian_age' => ['nullable', 'integer', 'min:0'],
            'guardian_gender' => ['nullable', 'string', 'max:50'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'family_history' => ['nullable', 'array'],
            'personal_non_pathological' => ['nullable', 'array'],
            'personal_pathological' => ['nullable', 'array'],
            'systems_review' => ['nullable', 'array'],
        ]);

        $record = MedicalRecord::create($validated);

        return redirect()
            ->route('admin.patients.show', $validated['patient_id'])
            ->with('success', 'Historia clínica registrada con éxito');
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'laterality' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'economic_dependency' => ['nullable', 'string', 'max:255'],
            'primary_caregiver' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_birth_date' => ['nullable', 'date'],
            'guardian_age' => ['nullable', 'integer', 'min:0'],
            'guardian_gender' => ['nullable', 'string', 'max:50'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'family_history' => ['nullable', 'array'],
            'personal_non_pathological' => ['nullable', 'array'],
            'personal_pathological' => ['nullable', 'array'],
            'systems_review' => ['nullable', 'array'],
        ]);

        $medicalRecord->update($validated);

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

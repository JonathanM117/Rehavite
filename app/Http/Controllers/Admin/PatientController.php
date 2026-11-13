<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return view('admin.patients.index');
    }

    public function create()
    {
        $therapists = User::orderBy('name')->get();

        return view('admin.patients.create', compact('therapists'));
    }

    /**
     * Reglas de validación compartidas por store() y update() para todos los
     * campos de la historia clínica, ahora unificados en la tabla patients.
     */
    private function clinicalRecordRules(): array
    {
        return [
            'manual_age'          => ['nullable', 'integer', 'min:0', 'max:130'],
            'marital_status'      => ['nullable', 'string', 'max:50'],
            'address'             => ['nullable', 'string', 'max:500'],
            'laterality'          => ['nullable', 'string', 'max:50'],
            'occupation'          => ['nullable', 'string', 'max:255'],
            'economic_dependency' => ['nullable', 'string', 'max:255'],
            'primary_caregiver'   => ['nullable', 'string', 'max:255'],
            'guardian_required'   => ['nullable', 'boolean'],
            'guardian_name'       => ['nullable', 'string', 'max:255'],
            'guardian_birth_date' => ['nullable', 'date'],
            'guardian_age'        => ['nullable', 'integer', 'min:0'],
            'guardian_gender'     => ['nullable', 'string', 'max:50'],
            'observations'        => ['nullable', 'string', 'max:2000'],
            'family_history'              => ['nullable', 'array'],
            'personal_non_pathological'   => ['nullable', 'array'],
            'personal_pathological'       => ['nullable', 'array'],
            'systems_review'               => ['nullable', 'array'],
            'pediatric_history'             => ['nullable', 'array'],

            // Rediseño del Expediente
            'body_regions'             => ['nullable', 'array'],
            'body_regions.*'           => ['string', 'max:50'],
            'phone_owner'              => ['nullable', 'string', 'max:50'],
            'alt_contact_name'         => ['nullable', 'string', 'max:255'],
            'alt_contact_relationship' => ['nullable', 'string', 'max:100'],
            'alt_contact_phone'        => ['nullable', 'string', 'max:20'],
            'referral_source'          => ['nullable', 'string', 'max:100'],
            'referral_source_other'    => ['nullable', 'string', 'max:255'],
            'referring_doctor'         => ['nullable', 'string', 'max:255'],
            'emergency_contact_name'            => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone'           => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship'    => ['nullable', 'string', 'max:100'],
            'birth_place'              => ['nullable', 'string', 'max:255'],
            'street_address'           => ['nullable', 'string', 'max:255'],
            'neighborhood'             => ['nullable', 'string', 'max:255'],
            'municipality'             => ['nullable', 'string', 'max:255'],
            'zone'                     => ['nullable', 'string', 'max:50'],
            'housing_type'             => ['nullable', 'string', 'max:100'],
            'transportation'           => ['nullable', 'string', 'max:100'],
            'architectural_barriers'   => ['nullable', 'array'],
            'architectural_barriers.*' => ['string', 'max:100'],
            'education_level'          => ['nullable', 'string', 'max:100'],
            'physical_build'           => ['nullable', 'string', 'max:50'],
            'stress_level'             => ['nullable', 'integer', 'min:0', 'max:10'],
            'work_demand'              => ['nullable', 'string', 'max:50'],
            'diet'                     => ['nullable', 'string', 'max:100'],
            'adl_independence'         => ['nullable', 'string', 'max:100'],
            'perceived_health'         => ['nullable', 'string', 'max:50'],
            'sleep_habits'             => ['nullable', 'string', 'max:100'],
            'pets'                     => ['nullable', 'array'],
            'pets.*'                   => ['string', 'max:50'],
            'non_pathological_notes'   => ['nullable', 'string', 'max:2000'],
            'medical_history_matrix'       => ['nullable', 'array'],
            'medical_history_deny_family'  => ['nullable', 'boolean'],
            'medical_history_deny_patient' => ['nullable', 'boolean'],
            'medical_history_notes'        => ['nullable', 'string', 'max:2000'],
            'symptoms_review'          => ['nullable', 'array'],
            'toxic_habits'             => ['nullable', 'array'],
            'toxic_habits.*'           => ['string', 'max:50'],
            'allergies'                => ['nullable', 'array'],
            'allergies.*'              => ['string', 'max:50'],
            'allergies_detail'         => ['nullable', 'string', 'max:2000'],
            'hospitalizations'         => ['nullable', 'array'],
            'surgeries'                => ['nullable', 'array'],
            'traumas'                  => ['nullable', 'array'],
            'current_medications'      => ['nullable', 'array'],
            'events_notes'             => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Las casillas de la matriz de antecedentes y del interrogatorio llegan
     * como '1'/ausente; se normalizan a booleanos reales antes de guardarlas.
     */
    private function normalizeClinicalBooleans(array $data): array
    {
        if (isset($data['medical_history_matrix']) && is_array($data['medical_history_matrix'])) {
            foreach ($data['medical_history_matrix'] as $key => $row) {
                $data['medical_history_matrix'][$key]['fam'] = !empty($row['fam']);
                $data['medical_history_matrix'][$key]['pac'] = !empty($row['pac']);
            }
        }
        if (isset($data['symptoms_review']) && is_array($data['symptoms_review'])) {
            foreach ($data['symptoms_review'] as $key => $row) {
                $data['symptoms_review'][$key]['presente'] = !empty($row['presente']);
            }
        }

        return $data;
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge([
            'name'        => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'max:150'],
            'phone'       => ['required', 'string', 'max:20'],
            'phone_own'   => ['nullable', 'string', 'max:20'],
            'phone_confirmation' => ['nullable', 'string', 'max:20'],
            'birth_date'  => ['required', 'date', 'after:' . now()->subYears(130)->toDateString(), 'before_or_equal:today'],
            'gender'      => ['required', 'in:masculino,femenino,otro,no_especificado'],
            'diagnosis'   => ['nullable', 'string', 'max:2000'],
            'created_at'  => ['nullable', 'date', 'after:1900-01-01', 'before_or_equal:today'],
        ], $this->clinicalRecordRules()), [
            'birth_date.required' => 'Corrige la fecha de nacimiento: este campo es obligatorio.',
            'birth_date.date' => 'Corrige la fecha de nacimiento: el formato no es válido.',
            'birth_date.after' => 'Corrige la fecha de nacimiento: no puede ser de hace más de 130 años.',
            'birth_date.before_or_equal' => 'Corrige la fecha de nacimiento: no puede ser una fecha futura.',
        ]);

        if (auth()->user()->admin && $request->has('user_id')) {
            $validated['user_id'] = $request->input('user_id');
        } else {
            $validated['user_id'] = auth()->id();
        }

        $validated['guardian_required'] = $request->boolean('guardian_required');
        $validated['medical_history_deny_family'] = $request->boolean('medical_history_deny_family');
        $validated['medical_history_deny_patient'] = $request->boolean('medical_history_deny_patient');
        $validated = $this->normalizeClinicalBooleans($validated);

        $patient = Patient::make($validated);
        if (auth()->user()->admin && $request->filled('created_at')) {
            $patient->created_at = $request->input('created_at');
        }
        if (empty($patient->manual_age) && $patient->birth_date) {
            $patient->manual_age = $patient->birth_date->age;
        }
        $patient->save();

        \App\Models\AuditLog::record('created', $patient, 'Creo nuevo expediente de paciente');

        try {
            $adminEmail = 'admin@rehavite.com';
            $therapistEmail = $patient->user ? $patient->user->email : null;

            $emails = array_filter([$adminEmail, $therapistEmail]);
            $emails = array_unique($emails);

            if (!empty($emails)) {
                \Illuminate\Support\Facades\Mail::to($emails)->send(new \App\Mail\PatientCreatedMail($patient));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al enviar correo de nuevo paciente: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.patients.show', $patient)
            ->with('success', 'Paciente registrado con éxito');
    }

    public function show(Patient $patient)
    {
        $user = auth()->user();

        // Authorization check: Admin, assigned therapist, or collaborator
        if (!$patient->canBeAccessedBy($user)) {
            return redirect()->route('admin.patients.index')
                ->with('error', 'No tienes permiso para ver este paciente.');
        }

        $patient->load(['consultations.evolutionNotes', 'consultations.studies', 'payments']);

        return view('admin.patients.show', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        // Dynamic validation: only validate fields that are present in the request
        // This avoids errors when archiving/restoring from simple panels
        $rules = [];

        if ($request->has('name'))       $rules['name']       = ['required', 'string', 'max:100'];
        if ($request->has('last_name'))  $rules['last_name']  = ['required', 'string', 'max:100'];
        if ($request->has('email'))      $rules['email']      = ['required', 'email', 'max:150'];
        if ($request->has('phone'))      $rules['phone']      = ['required', 'string', 'max:20'];
        if ($request->has('phone_own'))  $rules['phone_own']  = ['nullable', 'string', 'max:20'];
        if ($request->has('phone_confirmation')) $rules['phone_confirmation'] = ['nullable', 'string', 'max:20'];
        if ($request->has('birth_date')) $rules['birth_date'] = ['required', 'date', 'after:' . now()->subYears(130)->toDateString(), 'before_or_equal:today'];
        if ($request->has('gender'))     $rules['gender']     = ['required', 'in:masculino,femenino,otro,no_especificado'];
        if ($request->has('diagnosis'))  $rules['diagnosis']  = ['nullable', 'string', 'max:2000'];
        if ($request->has('status'))     $rules['status']     = ['nullable', 'string', 'in:activo,active,seguimiento,alta_clinica,discharged,suspendido,abandono,abandono_terapeutico,referido,inactive,pending_deletion,archived'];
        if ($request->has('user_id'))    $rules['user_id']    = ['nullable', 'exists:users,id'];
        if ($request->has('created_at')) $rules['created_at'] = ['nullable', 'date', 'after:1900-01-01', 'before_or_equal:today'];

        // Las casillas (body_regions, antecedentes, interrogatorio, etc.) no se
        // envían cuando todas quedan sin marcar, así que un envío completo del
        // formulario de Expediente (marcado por este campo oculto) debe poder
        // vaciarlas explícitamente en vez de dejar el dato anterior intacto.
        $isExpedienteFormSubmit = $request->has('expediente_form');
        $arrayContainerFields = [
            'body_regions', 'architectural_barriers', 'pets', 'toxic_habits', 'allergies',
            'medical_history_matrix', 'symptoms_review',
            'hospitalizations', 'surgeries', 'traumas', 'current_medications',
        ];

        foreach ($this->clinicalRecordRules() as $field => $fieldRules) {
            if ($request->has($field) || ($isExpedienteFormSubmit && in_array($field, $arrayContainerFields, true))) {
                $rules[$field] = $fieldRules;
            }
        }

        $validated = $request->validate($rules, [
            'birth_date.required' => 'Corrige la fecha de nacimiento: este campo es obligatorio.',
            'birth_date.date' => 'Corrige la fecha de nacimiento: el formato no es válido.',
            'birth_date.after' => 'Corrige la fecha de nacimiento: no puede ser de hace más de 130 años.',
            'birth_date.before_or_equal' => 'Corrige la fecha de nacimiento: no puede ser una fecha futura.',
        ]);

        if (!auth()->user()->admin) {
            unset($validated['user_id']);
        }

        if ($request->has('guardian_required')) {
            $validated['guardian_required'] = $request->boolean('guardian_required');
        }
        if ($request->has('medical_history_deny_family')) {
            $validated['medical_history_deny_family'] = $request->boolean('medical_history_deny_family');
        }
        if ($request->has('medical_history_deny_patient')) {
            $validated['medical_history_deny_patient'] = $request->boolean('medical_history_deny_patient');
        }
        if ($isExpedienteFormSubmit) {
            foreach ($arrayContainerFields as $field) {
                if (!$request->has($field)) {
                    $validated[$field] = [];
                }
            }
        }
        $validated = $this->normalizeClinicalBooleans($validated);

        $patient->fill($validated);
        if (auth()->user()->admin && $request->filled('created_at')) {
            $patient->created_at = $request->input('created_at');
        }
        $patient->save();

        AuditLog::record('updated', $patient, 'Expediente actualizado por ' . auth()->user()->name);

        return redirect()
            ->route('admin.patients.show', $patient)
            ->with('success', 'Paciente actualizado con éxito');
    }

    public function destroy(Patient $patient)
    {
        if (!auth()->user()->admin) {
            return redirect()->route('admin.patients.index')
                ->with('error', 'Solo los administradores pueden eliminar pacientes.');
        }

        $patient->delete();

        return redirect()
            ->route('admin.patients.index')
            ->with('success', 'Paciente eliminado con éxito');
    }

    // ─── Deletion Workflow (Fisio → Admin) ───────────────────────────────────

    public function requestDeletion(Patient $patient)
    {
        $user = auth()->user();

        // Only the assigned therapist or a collaborator (not admin) can request
        if ($user->admin) {
            return back()->with('error', 'Los administradores pueden eliminar directamente.');
        }
        if (!$patient->canBeAccessedBy($user)) {
            return back()->with('error', 'No tienes permiso sobre este paciente.');
        }
        if ($patient->isPendingDeletion()) {
            return back()->with('error', 'Ya existe una solicitud de eliminación pendiente para este paciente.');
        }

        // Mark as pending deletion
        $patient->update([
            'status'                  => 'pending_deletion',
            'deletion_requested_by'   => $user->id,
            'deletion_requested_at'   => now(),
        ]);

        return back()->with('success', '✅ Solicitud enviada. El expediente queda suspendido hasta la decisión de la administradora.');
    }

    public function confirmDeletion(Request $request, Patient $patient)
    {
        abort_unless(auth()->user()->admin, 403);

        $name = $patient->full_name;
        
        // Clear pending status and soft delete it
        $patient->update([
            'status' => 'active',
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);
        $patient->delete();

        return redirect()->route('admin.patients.index')
            ->with('success', "Paciente {$name} enviado a la papelera.");
    }

    public function rejectDeletion(Request $request, Patient $patient)
    {
        abort_unless(auth()->user()->admin, 403);

        $patient->update([
            'status'                => 'active',
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        return redirect()->route('admin.patients.index')
            ->with('success', "Solicitud rechazada. El paciente {$patient->full_name} ha sido reactivado.");
    }

    public function archivePatient(Request $request, Patient $patient)
    {
        abort_unless(auth()->user()->admin, 403);

        $patient->update([
            'status'                => 'archived',
            'deletion_requested_by' => null,
            'deletion_requested_at' => null,
        ]);

        \App\Models\AuditLog::record('archived', $patient, 'Expediente archivado por el administrador');

        return redirect()->route('admin.patients.index')
            ->with('success', "Paciente {$patient->full_name} archivado correctamente.");
    }

    public function archived()
    {
        abort_unless(auth()->user()->admin, 403);

        $patients = \App\Models\Patient::withTrashed()
            ->whereIn('status', ['archived', 'pending_deletion'])
            ->with(['user', 'deletionRequestedBy'])
            ->orderByRaw("FIELD(status, 'pending_deletion', 'archived')")
            ->orderBy('deletion_requested_at', 'desc')
            ->paginate(20);

        return view('admin.patients.archived', compact('patients'));
    }

    // ─── Trash Workflow ──────────────────────────────────────────────────────

    public function trashed()
    {
        abort_unless(auth()->user()->admin, 403);

        // Get only soft-deleted patients
        $patients = \App\Models\Patient::onlyTrashed()
            ->with(['user'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.patients.trashed', compact('patients'));
    }

    public function restoreTrashed($id)
    {
        abort_unless(auth()->user()->admin, 403);

        $patient = \App\Models\Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('admin.patients.trashed')
            ->with('success', "Paciente {$patient->full_name} restaurado correctamente.");
    }

    public function forceDestroy($id)
    {
        abort_unless(auth()->user()->admin, 403);

        $patient = \App\Models\Patient::onlyTrashed()->findOrFail($id);
        $name = $patient->full_name;
        $patient->forceDelete();

        return redirect()->route('admin.patients.trashed')
            ->with('success', "Paciente {$name} eliminado permanentemente.");
    }
}

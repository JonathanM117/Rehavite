<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
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
        ], [
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

        $patient = Patient::make($validated);
        if (auth()->user()->admin && $request->filled('created_at')) {
            $patient->created_at = $request->input('created_at');
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

        $patient->load(['medicalRecord', 'consultations.evolutionNotes', 'consultations.studies', 'payments']);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $therapists = User::orderBy('name')->get();
        return view('admin.patients.edit', compact('patient', 'therapists'));
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

        $validated = $request->validate($rules, [
            'birth_date.required' => 'Corrige la fecha de nacimiento: este campo es obligatorio.',
            'birth_date.date' => 'Corrige la fecha de nacimiento: el formato no es válido.',
            'birth_date.after' => 'Corrige la fecha de nacimiento: no puede ser de hace más de 130 años.',
            'birth_date.before_or_equal' => 'Corrige la fecha de nacimiento: no puede ser una fecha futura.',
        ]);

        if (!auth()->user()->admin) {
            unset($validated['user_id']);
        }

        $patient->fill($validated);
        if (auth()->user()->admin && $request->filled('created_at')) {
            $patient->created_at = $request->input('created_at');
        }
        $patient->save();

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

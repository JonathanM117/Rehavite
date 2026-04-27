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
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:masculino,femenino,otro,no_especificado'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:masculino,femenino,otro,no_especificado'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:active,inactive,discharged'],
            'user_id' => ['nullable', 'exists:users,id'],
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
        $patient->delete();

        return redirect()
            ->route('admin.patients.index')
            ->with('success', 'Paciente eliminado con éxito');
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\User;
use App\Models\AuditLog;

class PatientDetail extends Component
{
    public Patient $patient;
    public $activeTab = 'expediente';
    protected $queryString = ['activeTab'];
    protected $listeners = [
        'clearSelectedConsultation',
        'consultationSaved' => 'handleConsultationSaved',
        'cancelNewConsultation' => 'handleCancelNewConsultation',
    ];
    public $showMedicalRecordForm = false;
    public $showConsultationForm = false;
    public $showCollaboratorModal = false;
    public $selectedCollaboratorId = '';
    public $selectedConsultationId = null;
    public $newlyCreatedConsultationId = null;

    public function mount(Patient $patient)
    {
        $user = auth()->user();

        // Authorization Check (owner, collaborator, or admin)
        if (!$patient->canBeAccessedBy($user)) {
            return redirect()->route('admin.patients.index')
                ->with('error', 'No tienes permiso para ver este paciente.');
        }

        $this->patient = $patient->load([
            'user',
            'medicalRecord',
            'consultations.evolutionNotes',
            'consultations.studies',
            'consultations.exercises',
            'clinicalDocuments',
            'collaborators',
        ]);
    }

    /**
     * List of therapists available to add as collaborators
     * (excludes owner and already-assigned collaborators).
     */
    public function getAvailableCollaboratorsProperty()
    {
        $excludeIds = $this->patient->collaborators->pluck('id')->push($this->patient->user_id)->toArray();
        return User::whereNotIn('id', $excludeIds)->orderBy('name')->get();
    }

    public function openCollaboratorModal()
    {
        $this->patient->load('collaborators');
        unset($this->availableCollaborators); // Clear computed cache
        $this->selectedCollaboratorId = '';
        $this->showCollaboratorModal = true;
    }

    public function addCollaborator()
    {
        if (!$this->selectedCollaboratorId)
            return;

        $this->patient->collaborators()->attach($this->selectedCollaboratorId, [
            'granted_by' => auth()->id(),
        ]);

        $collaborator = User::find($this->selectedCollaboratorId);
        AuditLog::record('updated', $this->patient, 'Asigno a ' . ($collaborator->name ?? '') . ' como colaborador');

        $this->selectedCollaboratorId = '';
        $this->patient->load('collaborators');
        unset($this->availableCollaborators); // Refresh list
    }

    public function removeCollaborator($userId)
    {
        $collaborator = User::find($userId);
        $this->patient->collaborators()->detach($userId);

        AuditLog::record('updated', $this->patient, 'Removio a ' . ($collaborator->name ?? '') . ' como colaborador');

        $this->patient->load('collaborators');
        unset($this->availableCollaborators); // Refresh list
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedConsultationId = null; // Reset selection when changing tabs
        $this->newlyCreatedConsultationId = null;
    }

    public function selectConsultation($id)
    {
        $this->selectedConsultationId = $id;
        $this->newlyCreatedConsultationId = null;
    }

    /*public function clearSelectedConsultation()
    {
        if ($this->newlyCreatedConsultationId) {
            $newConsultation = \App\Models\Consultation::find($this->newlyCreatedConsultationId);
            if ($newConsultation && empty($newConsultation->reason)) {
                $newConsultation->delete();
                return redirect()->route('admin.patients.show', [$this->patient->id, 'activeTab' => 'consultas']);
            }
        }
        $this->selectedConsultationId = null;
        $this->newlyCreatedConsultationId = null;
        $this->patient->load('consultations'); // reload updates
    }*/

    public function clearSelectedConsultation()
    {
        // Ya no eliminamos el modelo aquí, solo limpiamos el estado visual
        $this->selectedConsultationId = null;
        $this->newlyCreatedConsultationId = null;
        $this->patient->load('consultations'); // Recargamos para reflejar cambios
    }

    public function handleConsultationSaved($id)
    {
        if ($this->newlyCreatedConsultationId == $id) {
            $this->newlyCreatedConsultationId = null;
        }
        $this->patient->load('consultations');
    }

    public function handleCancelNewConsultation($consultationId)
    {
        // Eliminar la consulta vacía desde el padre (evita conflicto de hidratación de Livewire)
        $consultation = \App\Models\Consultation::find($consultationId);
        if ($consultation && empty($consultation->reason)) {
            $consultation->delete();
        }

        // Limpiar estado del padre
        $this->selectedConsultationId = null;
        $this->newlyCreatedConsultationId = null;

        // Recargar consultas del paciente
        $this->patient->load('consultations');
    }

    public function createConsultation()
    {
        $consultation = \App\Models\Consultation::create([
            'patient_id' => $this->patient->id,
            'user_id' => auth()->id(),
            'reason' => '',
            'status' => null
        ]);

        \App\Models\AuditLog::record('created', $consultation, 'Creó nueva consulta desde el perfil del paciente');

        $this->patient->load('consultations');
        $this->selectedConsultationId = $consultation->id;
        $this->newlyCreatedConsultationId = $consultation->id;
    }

    public function toggleMedicalRecordForm()
    {
        $this->showMedicalRecordForm = !$this->showMedicalRecordForm;
    }

    public function render()
    {
        return view('livewire.patient-detail');
    }
}

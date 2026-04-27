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
    public $selectedConsultationId = null;
    public $showMedicalRecordForm = false;
    public $showConsultationForm = false;
    public $showPaymentForm = false;
    public $showCollaboratorModal = false;
    public $selectedCollaboratorId = '';
    public $consultationTab = 'evolution';

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
            'payments',
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
        if (!$this->selectedCollaboratorId) return;

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
        $this->selectedConsultationId = null;
    }

    public function selectConsultation($id)
    {
        $this->selectedConsultationId = $id;
    }

    public function closeConsultation()
    {
        $this->selectedConsultationId = null;
        $this->consultationTab = 'evolution'; // Reset to default when closing
    }

    public function setConsultationTab($tab)
    {
        $this->consultationTab = $tab;
    }

    public function toggleMedicalRecordForm()
    {
        $this->showMedicalRecordForm = !$this->showMedicalRecordForm;
    }

    public function toggleConsultationForm()
    {
        $this->showConsultationForm = !$this->showConsultationForm;
    }

    public function togglePaymentForm()
    {
        $this->showPaymentForm = !$this->showPaymentForm;
    }

    public function render()
    {
        return view('livewire.patient-detail');
    }
}

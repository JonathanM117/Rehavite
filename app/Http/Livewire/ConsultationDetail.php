<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Consultation;

class ConsultationDetail extends Component
{
    public $consultationId;
    public $activeTab = 'informacion'; // informacion, notas, estudios, ejercicios, escalas, fotos, documentos
    public $selectedDocumentToLink = '';
    public $isNew = false;

    public $editingConsultationId = null;
    public $patientId = null;

    // Form fields for editing
    public $reason;
    public $diagnosis;
    public $limitations_restrictions;
    public $clinical_flags = [];
    public $current_condition = [];
    public $physical_exam = [];
    public $prognosis;
    public $treatment_objectives;
    public $previous_studies_notes;
    public $discharge_recommendations;
    public $discharge_reason;
    public $status;

    #[Computed]
    public function consultation()
    {
        return Consultation::with([
            'patient.user',
            'evolutionNotes',
            'studies',
            'exercises',
            'clinicalScales',
            'clinicalPhotos',
            'clinicalDocuments',
        ])->find($this->consultationId);
    }

    /**
     * Documentos del repositorio del paciente que aún no están vinculados a este episodio.
     */
    #[Computed]
    public function availableDocumentsToLink()
    {
        $consultation = $this->consultation;
        if (!$consultation) {
            return collect();
        }

        $linkedIds = $consultation->clinicalDocuments->pluck('id');

        return \App\Models\ClinicalDocument::where('patient_id', $consultation->patient_id)
            ->whereNotIn('id', $linkedIds)
            ->orderByDesc('date')
            ->get();
    }

    public function linkDocument()
    {
        if (!$this->selectedDocumentToLink) {
            return;
        }

        $this->consultation->clinicalDocuments()->syncWithoutDetaching([$this->selectedDocumentToLink]);
        $this->selectedDocumentToLink = '';
        unset($this->consultation, $this->availableDocumentsToLink);
    }

    public function unlinkDocument($documentId)
    {
        $this->consultation->clinicalDocuments()->detach($documentId);
        unset($this->consultation, $this->availableDocumentsToLink);
    }

    public function mount(Consultation $consultation, $isNew = false)
    {
        $this->isNew = $isNew;
        $user = auth()->user();
        $this->consultationId = $consultation->id;
        $this->patientId = $consultation->patient_id;

        // Authorization Check
        if (!$consultation->patient->canBeAccessedBy($user)) {
            return redirect()->route('admin.patients.index')
                ->with('error', 'No tienes permiso para ver esta consulta.');
        }

        $this->fillForm($consultation);

        if ($this->isNew || request()->query('edit')) {
            $this->editingConsultationId = $consultation->id;
        }
    }

    public function goBack()
    {
        $consultation = $this->consultation;
        // Si es una consulta nueva y está vacía, avisamos al padre para que la elimine localmente
        if ($this->isNew && (!$consultation || empty($consultation->reason))) {
            $this->dispatch('cancelNewConsultation', consultationId: $this->consultationId);
            return;
        }

        // Si no es nueva o ya tiene datos, simplemente le avisamos al padre que cierre la vista
        $this->dispatch('clearSelectedConsultation');
    }

    public function fillForm(?Consultation $consultation = null)
    {
        $consultation = $consultation ?? $this->consultation;
        if (!$consultation) {
            return;
        }
        $this->reason = $consultation->reason;
        $this->diagnosis = $consultation->diagnosis;
        $this->limitations_restrictions = $consultation->limitations_restrictions;
        $this->clinical_flags = $consultation->clinical_flags ?? Consultation::clinicalFlagsDefaults();
        $this->current_condition = $consultation->current_condition ?? [];
        $this->physical_exam = $consultation->physical_exam ?? [];
        $this->prognosis = $consultation->prognosis;
        $this->treatment_objectives = $consultation->treatment_objectives;
        $this->previous_studies_notes = $consultation->previous_studies_notes;
        $this->discharge_recommendations = $consultation->discharge_recommendations;
        $this->discharge_reason = $consultation->discharge_reason;
        $this->status = $consultation->status;
    }

    public function editConsultation()
    {
        $consultation = $this->consultation;

        $this->editingConsultationId = $this->consultationId;
        $this->fillForm();
    }

    public function cancelEdit()
    {
        if ($this->isNew) {
            // Avisamos al padre para que maneje la eliminación local
            $this->dispatch('cancelNewConsultation', consultationId: $this->consultationId);
            return;
        }
        $this->editingConsultationId = null;
        $this->fillForm();
    }

    private function cap(?string $v): ?string
    {
        if (is_null($v) || trim($v) === '') return $v;
        $v = trim($v);
        return mb_strtoupper(mb_substr($v, 0, 1)) . mb_substr($v, 1);
    }

    private function capArray(?array $arr): ?array
    {
        if (is_null($arr))
            return null;
        return array_map(fn($v) => is_string($v) ? $this->cap($v) : $v, $arr);
    }

    public function saveConsultation()
    {
        $consultation = $this->consultation;
        if (!$consultation) {
            return;
        }



        $this->validate([
            'reason' => 'required|string|max:2000',
            'diagnosis' => 'nullable|string|max:2000',
            'limitations_restrictions' => 'nullable|string|max:5000',
            'clinical_flags' => 'nullable|array',
            'prognosis' => 'nullable|string|max:2000',
            'treatment_objectives' => 'nullable|string|max:2000',
            'previous_studies_notes' => 'nullable|string|max:2000',
            'discharge_recommendations' => 'nullable|string|max:2000',
            'discharge_reason' => 'nullable|string|max:2000',
            'status' => 'nullable|in:valoracion,activo,seguimiento,alta_clinica,suspendido,abandono,referido,inactivo',
        ]);

        $consultation->update([
            'reason' => $this->cap($this->reason),
            'diagnosis' => $this->cap($this->diagnosis),
            'limitations_restrictions' => $this->limitations_restrictions,
            'clinical_flags' => $this->clinical_flags,
            'current_condition' => $this->capArray($this->current_condition),
            'physical_exam' => $this->capArray($this->physical_exam),
            'prognosis' => $this->cap($this->prognosis),
            'treatment_objectives' => $this->cap($this->treatment_objectives),
            'previous_studies_notes' => $this->cap($this->previous_studies_notes),
            'discharge_recommendations' => $this->cap($this->discharge_recommendations),
            'discharge_reason' => $this->cap($this->discharge_reason),
            'status' => $this->status,
        ]);

        $this->isNew = false;
        $this->editingConsultationId = null;
        $consultation->refresh();
        $this->dispatch('consultationSaved', id: $consultation->id);
        session()->flash('success', 'Consulta actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.consultation-detail')
            ->extends('layouts.admin')
            ->section('content');
    }
}

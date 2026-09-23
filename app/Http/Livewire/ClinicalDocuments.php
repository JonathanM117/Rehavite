<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ClinicalDocument;
use App\Models\Exercise;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;

class ClinicalDocuments extends Component
{
    use WithFileUploads;

    public $patientId;
    public $patient;
    public $documents = [];
    public $exercises = [];
    public $showForm = false;
    public $typeFilter = '';

    // Form fields
    public $date;
    public $type = 'Consentimiento informado';
    public $description;
    public $file;
    public $uploadProgress = 0;
    public $valid_until;
    public $manage_status_manually = false;
    public $manual_status = 'vigente';

    // Edit mode
    public $editingDocId = null;

    protected function rules()
    {
        return [
            'date' => 'required|date',
            'type' => 'required|string',
            'description' => 'nullable|string|max:500',
            'file' => ($this->editingDocId ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'valid_until' => 'nullable|date',
            'manage_status_manually' => 'boolean',
            'manual_status' => 'nullable|in:vigente,actualizar,historico',
        ];
    }

    protected $messages = [
        'file.required' => 'Debes seleccionar un documento.',
        'file.file' => 'El archivo no es válido.',
        'file.mimes' => 'Solo se permiten documentos PDF, JPG o PNG.',
        'file.max' => 'El documento no debe superar los 20 MB.',
    ];

    public function mount($patientId)
    {
        $this->patientId = $patientId;
        $this->date = date('Y-m-d');
        $this->loadData();
    }

    public function updatedFile()
    {
        $extension = strtolower($this->file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($extension, $allowedExtensions)) {
            $this->reset('file');
            $this->dispatch('alert', message: 'Este formato de archivo (.' . $extension . ') no es compatible. Por favor usa PDF, JPG o PNG.');
        }
    }

    public function loadData()
    {
        if (!$this->patientId) return;

        $this->patient = Patient::findOrFail($this->patientId);
        $this->documents = $this->patient->clinicalDocuments()
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->get();

        $this->exercises = Exercise::with('consultation')
            ->whereIn('consultation_id', $this->patient->consultations()->pluck('id'))
            ->orderByDesc('date')
            ->get();
    }

    public function updatedTypeFilter()
    {
        $this->loadData();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->editingDocId = null;
        $this->date = date('Y-m-d');
        $this->type = 'Consentimiento informado';
        $this->description = null;
        $this->file = null;
        $this->valid_until = null;
        $this->manage_status_manually = false;
        $this->manual_status = 'vigente';
        $this->resetErrorBag();
    }

    public function editDoc($id)
    {
        $doc = ClinicalDocument::find($id);
        if (!$doc) return;

        $this->editingDocId = $id;
        $this->date = $doc->date?->format('Y-m-d');
        $this->type = $doc->type;
        $this->description = $doc->description;
        $this->file = null;
        $this->valid_until = $doc->valid_until?->format('Y-m-d');
        $this->manage_status_manually = $doc->manage_status_manually;
        $this->manual_status = $doc->manual_status ?: 'vigente';
        $this->showForm = true;
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function saveDoc()
    {
        $this->validate();

        $data = [
            'patient_id' => $this->patientId,
            'date' => $this->date,
            'type' => $this->type,
            'description' => $this->description,
            'valid_until' => $this->type === 'Incapacidad médica' ? $this->valid_until : null,
            'manage_status_manually' => $this->manage_status_manually,
            'manual_status' => $this->manage_status_manually ? $this->manual_status : null,
        ];

        if ($this->file) {
            $filename = time() . '_' . $this->file->getClientOriginalName();
            $path = $this->file->storeAs('clinical_documents/' . $this->patientId, $filename, 'public');
            $data['file_path'] = $path;
        }

        if ($this->editingDocId) {
            $doc = ClinicalDocument::find($this->editingDocId);
            // Delete old file if new file uploaded
            if ($this->file && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->update($data);
            session()->flash('success', 'Documento actualizado correctamente.');
        } else {
            ClinicalDocument::create($data);
            session()->flash('success', 'Documento guardado correctamente.');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->loadData();
    }

    public function deleteDoc($id)
    {
        $doc = ClinicalDocument::find($id);
        if ($doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
            session()->flash('success', 'Documento eliminado correctamente.');
            $this->loadData();
        }
    }

    public function render()
    {
        return view('livewire.clinical-documents', [
            'documentTypes' => ClinicalDocument::TYPES,
        ]);
    }
}

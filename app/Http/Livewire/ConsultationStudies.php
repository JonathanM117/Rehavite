<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Study;
use App\Models\Consultation;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Reactive;

class ConsultationStudies extends Component
{
    use WithFileUploads;

    #[Reactive]
    public $consultationId;
    public $consultation;
    public $studies = [];
    public $showForm = false;

    // Form fields
    public $date;
    public $type = 'otro';
    public $description;
    public $file;
    public $uploadProgress = 0;

    protected $rules = [
        'date' => 'required|date',
        'type' => 'required|string',
        'description' => 'nullable|string|max:500',
        'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240', // 10MB max
    ];

    protected $messages = [
        'file.required' => 'Debes seleccionar un archivo.',
        'file.file' => 'El archivo no es válido.',
        'file.mimes' => 'Solo se permiten imágenes (JPG, PNG, WEBP) o documentos PDF.',
        'file.max' => 'El archivo no debe superar los 10MB.',
    ];

    public function mount($consultationId = null)
    {
        $this->consultationId = $consultationId;
        $this->date = date('Y-m-d');
        $this->loadData();
    }

    public function updatedFile()
    {
        $extension = strtolower($this->file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];

        if (!in_array($extension, $allowedExtensions)) {
            $this->reset('file');
            $this->dispatch('alert', message: 'Este formato de archivo (.' . $extension . ') no es compatible. Por favor usa JPG, PNG, WEBP o PDF.');
        }
    }

    public function updatedConsultationId()
    {
        $this->showForm = false;
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->consultationId) {
            $this->studies = [];
            $this->consultation = null;
            return;
        }

        $this->consultation = Consultation::with('studies')->findOrFail($this->consultationId);
        $this->studies = $this->consultation->studies()->orderByDesc('date')->get();
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
        $this->date = date('Y-m-d');
        $this->type = 'otro';
        $this->description = null;
        $this->file = null;
        $this->resetErrorBag();
    }

    public function saveStudy()
    {
        $this->validate();

        // 1. Storage Path: studies/{consultation_id}/{filename}
        $filename = time() . '_' . $this->file->getClientOriginalName();
        $path = $this->file->storeAs('studies/' . $this->consultationId, $filename, 'public');

        // 2. Create Study Record
        Study::create([
            'consultation_id' => $this->consultationId,
            'date' => $this->date,
            'type' => $this->type,
            'description' => $this->description,
            'file_path' => $path,
        ]);

        $this->resetForm();
        $this->showForm = false;
        $this->loadData();
        
        session()->flash('success', 'Estudio guardado correctamente.');
    }

    public function deleteStudy($id)
    {
        $study = Study::findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($study->file_path)) {
            Storage::disk('public')->delete($study->file_path);
        }
        
        $study->delete();
        $this->loadData();
        
        session()->flash('success', 'Estudio eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.consultation-studies');
    }
}

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
    public $studies = [];
    public $showForm = false;

    // Form fields
    public $date;
    public $type = 'otro';
    public $description;
    public $file;
    public $uploadProgress = 0;
    
    // Edit mode
    public $editingStudyId = null;

    protected function rules()
    {
        return [
            'date' => 'required|date',
            'type' => 'required|string',
            'description' => 'nullable|string|max:500',
            'file' => ($this->editingStudyId ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ];
    }

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
            return;
        }

        $this->studies = Study::where('consultation_id', $this->consultationId)
            ->orderByDesc('date')
            ->get();
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
        $this->editingStudyId = null;
        $this->date = date('Y-m-d');
        $this->type = 'otro';
        $this->description = null;
        $this->file = null;
        $this->resetErrorBag();
    }

    public function editStudy($id)
    {
        $study = Study::find($id);
        if (!$study) return;

        $this->editingStudyId = $id;
        $this->date = $study->date?->format('Y-m-d');
        $this->type = $study->type;
        $this->description = $study->description;
        $this->file = null;
        $this->showForm = true;
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function saveStudy()
    {
        $this->validate();

        $data = [
            'consultation_id' => $this->consultationId,
            'date' => $this->date,
            'type' => $this->type,
            'description' => $this->description,
        ];

        if ($this->file) {
            $filename = time() . '_' . $this->file->getClientOriginalName();
            $path = $this->file->storeAs('studies/' . $this->consultationId, $filename, 'public');
            $data['file_path'] = $path;
        }

        if ($this->editingStudyId) {
            $study = Study::find($this->editingStudyId);
            // Delete old file if new file uploaded
            if ($this->file && Storage::disk('public')->exists($study->file_path)) {
                Storage::disk('public')->delete($study->file_path);
            }
            $study->update($data);
            session()->flash('success', 'Estudio actualizado correctamente.');
        } else {
            Study::create($data);
            session()->flash('success', 'Estudio guardado correctamente.');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->loadData();
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

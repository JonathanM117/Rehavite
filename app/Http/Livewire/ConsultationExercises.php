<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Exercise;
use App\Models\Consultation;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Reactive;

class ConsultationExercises extends Component
{
    use WithFileUploads;

    #[Reactive]
    public $consultationId;
    public $consultation;
    public $exercises = [];
    public $showForm = false;

    // Form fields
    public $date;
    public $title;
    public $description;
    public $file;

    protected $rules = [
        'date' => 'required|date',
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:500',
        'file' => 'required|file|mimes:pdf|max:10240', // 10MB max, solo PDF
    ];

    protected $messages = [
        'file.required' => 'Debes seleccionar un archivo PDF.',
        'file.file' => 'El archivo no es válido.',
        'file.mimes' => 'Solo se permiten archivos PDF.',
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

        if ($extension !== 'pdf') {
            $this->reset('file');
            $this->dispatch('alert', message: 'Solo se permiten archivos PDF para ejercicios.');
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
            $this->exercises = [];
            $this->consultation = null;
            return;
        }

        $this->consultation = Consultation::with('exercises')->findOrFail($this->consultationId);
        $this->exercises = $this->consultation->exercises()->orderByDesc('date')->get();
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
        $this->title = null;
        $this->description = null;
        $this->file = null;
        $this->resetErrorBag();
    }

    public function saveExercise()
    {
        $this->validate();

        // Storage Path: exercises/{consultation_id}/{filename}
        $filename = time() . '_' . $this->file->getClientOriginalName();
        $path = $this->file->storeAs('exercises/' . $this->consultationId, $filename, 'public');

        Exercise::create([
            'consultation_id' => $this->consultationId,
            'date' => $this->date,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $path,
        ]);

        $this->resetForm();
        $this->showForm = false;
        $this->loadData();

        session()->flash('success', 'Ejercicio guardado correctamente.');
    }

    public function deleteExercise($id)
    {
        $exercise = Exercise::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($exercise->file_path)) {
            Storage::disk('public')->delete($exercise->file_path);
        }

        $exercise->delete();
        $this->loadData();

        session()->flash('success', 'Ejercicio eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.consultation-exercises');
    }
}

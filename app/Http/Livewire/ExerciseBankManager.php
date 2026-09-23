<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class ExerciseBankManager extends Component
{
    use WithFileUploads;

    public $file;
    public $currentFile = null;

    protected $messages = [
        'file.required' => 'Debes seleccionar un archivo HTML.',
        'file.max' => 'El archivo no debe superar los 20MB.',
    ];

    public function mount()
    {
        $this->loadCurrentFile();
    }

    public function loadCurrentFile()
    {
        $path = SystemSetting::get('exercise_bank_path');

        if (!$path || !Storage::disk('public')->exists($path)) {
            $this->currentFile = null;
            return;
        }

        $this->currentFile = [
            'path'          => $path,
            'original_name' => SystemSetting::get('exercise_bank_original_name'),
            'uploaded_at'   => SystemSetting::get('exercise_bank_uploaded_at'),
            'uploaded_by'   => SystemSetting::get('exercise_bank_uploaded_by'),
            'size'          => Storage::disk('public')->size($path),
        ];
    }

    public function updatedFile()
    {
        $extension = strtolower($this->file->getClientOriginalExtension());

        if (!in_array($extension, ['html', 'htm'])) {
            $this->reset('file');
            $this->addError('file', 'Solo se permiten archivos HTML (.html).');
        }
    }

    public function save()
    {
        $this->validate([
            'file' => 'required|file|max:20480',
        ]);

        $extension = strtolower($this->file->getClientOriginalExtension());
        if (!in_array($extension, ['html', 'htm'])) {
            $this->addError('file', 'Solo se permiten archivos HTML (.html).');
            return;
        }

        $contents = file_get_contents($this->file->getRealPath());
        if (stripos($contents, '<html') === false) {
            $this->addError('file', 'El archivo no parece ser una página HTML válida.');
            return;
        }

        $filename = 'banco-ejercicios-' . now()->format('Y-m-d_His') . '.html';
        $path = $this->file->storeAs('exercise-bank', $filename, 'public');

        SystemSetting::set('exercise_bank_path', $path);
        SystemSetting::set('exercise_bank_original_name', $this->file->getClientOriginalName());
        SystemSetting::set('exercise_bank_uploaded_at', now()->toDateTimeString());
        SystemSetting::set('exercise_bank_uploaded_by', auth()->user()->name);

        $this->reset('file');
        session()->flash('success', 'Banco de ejercicios actualizado correctamente. Ya está disponible para todos los pacientes.');
        $this->loadCurrentFile();
    }

    public function render()
    {
        return view('livewire.exercise-bank-manager');
    }
}

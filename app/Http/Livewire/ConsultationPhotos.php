<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ClinicalPhoto;
use App\Models\Consultation;
use Illuminate\Support\Facades\Storage;

class ConsultationPhotos extends Component
{
    use WithFileUploads;

    public $consultationId;
    public $patientId;
    
    public $photoFile;
    public $title = '';
    public $description = '';
    public $date = '';

    public $showUploadForm = false;
    
    // Side-by-side comparison selection
    public $comparePhotoId1 = '';
    public $comparePhotoId2 = '';

    public function mount($consultationId)
    {
        $this->consultationId = $consultationId;
        $consultation = Consultation::find($consultationId);
        $this->patientId = $consultation?->patient_id;
        $this->date = date('Y-m-d');
    }

    public function toggleUploadForm()
    {
        $this->showUploadForm = !$this->showUploadForm;
        if ($this->showUploadForm) {
            $this->photoFile = null;
            $this->title = '';
            $this->description = '';
            $this->date = date('Y-m-d');
        }
    }

    public function uploadPhoto()
    {
        $this->validate([
            'photoFile' => 'required|image|max:10240', // max 10MB
            'date' => 'required|date',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ], [
            'photoFile.required' => 'Debes seleccionar una imagen.',
            'photoFile.image' => 'El archivo debe ser una imagen válida.',
            'photoFile.max' => 'La imagen no debe pesar más de 10MB.',
        ]);

        // Enforce 48-hour consultation lock for uploading photos
        $consultation = Consultation::find($this->consultationId);
        if ($consultation && !auth()->user()->admin && $consultation->created_at && $consultation->created_at->diffInHours(now()) > 48) {
            session()->flash('error', 'No se pueden añadir fotos clínicas en una consulta de más de 48 horas.');
            return;
        }

        // Store the photo file in the public disk
        $path = $this->photoFile->store('clinical-photos', 'public');

        ClinicalPhoto::create([
            'patient_id' => $this->patientId,
            'consultation_id' => $this->consultationId,
            'date' => $this->date,
            'file_path' => '/storage/' . $path,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Foto clínica subida correctamente.');
        $this->showUploadForm = false;
        $this->photoFile = null;
        $this->title = '';
        $this->description = '';
    }

    public function deletePhoto($id)
    {
        // Enforce 48-hour consultation lock
        $consultation = Consultation::find($this->consultationId);
        if ($consultation && !auth()->user()->admin && $consultation->created_at && $consultation->created_at->diffInHours(now()) > 48) {
            session()->flash('error', 'No se pueden eliminar fotos en una consulta de más de 48 horas.');
            return;
        }

        $photo = ClinicalPhoto::find($id);
        if ($photo) {
            // Delete actual file from storage if it exists
            $relativeStoragePath = str_replace('/storage/', '', $photo->file_path);
            if (Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }
            
            $photo->delete();
            session()->flash('success', 'Foto eliminada correctamente.');

            if ($this->comparePhotoId1 == $id) $this->comparePhotoId1 = '';
            if ($this->comparePhotoId2 == $id) $this->comparePhotoId2 = '';
        }
    }

    public function render()
    {
        // Fetch all photos for this patient so the user can compare progression over time
        $allPatientPhotos = ClinicalPhoto::where('patient_id', $this->patientId)
            ->orderBy('date', 'desc')
            ->get();

        $photo1 = $this->comparePhotoId1 ? ClinicalPhoto::find($this->comparePhotoId1) : null;
        $photo2 = $this->comparePhotoId2 ? ClinicalPhoto::find($this->comparePhotoId2) : null;

        return view('livewire.consultation-photos', [
            'allPatientPhotos' => $allPatientPhotos,
            'photo1' => $photo1,
            'photo2' => $photo2,
        ]);
    }
}

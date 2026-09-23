<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Consultation;
use App\Models\EvolutionNote;
use Livewire\Attributes\Reactive;

class EvolutionNotes extends Component
{
    #[Reactive]
    public $consultationId;
    public $notes = [];
    public $showForm = false;

    // Form fields
    public $date;
    public $session_number;
    public $description;
    public $vital_signs = [];
    public $plan;
    public $treatment;
    public $additional_notes;
    public $comments;

    // Edit mode
    public $editingNoteId = null;

    public function mount($consultationId = null)
    {
        $this->consultationId = $consultationId;
        $this->vital_signs = EvolutionNote::vitalSignsDefaults();
        $this->loadData();
    }

    public function updatedConsultationId()
    {
        $this->showForm = false;
        $this->editingNoteId = null;
        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->consultationId) {
            $this->notes = [];
            return;
        }

        $this->notes = EvolutionNote::with('user')
            ->where('consultation_id', $this->consultationId)
            ->get()
            ->sortByDesc('date');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->resetForm();
    }

    public function saveNote()
    {


        $this->validate([
            'date' => 'required|date',
            'session_number' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'plan' => 'nullable|string|max:2000',
            'treatment' => 'nullable|string|max:2000',
            'additional_notes' => 'nullable|string|max:2000',
            'comments' => 'nullable|string|max:2000',
        ]);

        $data = [
            'consultation_id' => $this->consultationId,
            'user_id' => auth()->id(),
            'date' => $this->date,
            'session_number' => $this->session_number,
            'description' => $this->description,
            'vital_signs' => $this->vital_signs,
            'plan' => $this->plan,
            'treatment' => $this->treatment,
            'additional_notes' => $this->additional_notes,
            'comments' => $this->comments,
        ];

        if ($this->editingNoteId) {
            $note = EvolutionNote::find($this->editingNoteId);
            $note->update($data);
        } else {
            $note = EvolutionNote::create($data);
        }

        $savedId = $note->id;

        $this->resetForm();
        $this->showForm = false;
        $this->loadData();

        $this->dispatch('note-saved', id: $savedId);
    }

    public function editNote($noteId)
    {
        $note = EvolutionNote::find($noteId);
        if (!$note)
            return;



        $this->editingNoteId = $noteId;
        $this->date = $note->date?->format('Y-m-d');
        $this->session_number = $note->session_number;
        $this->description = $note->description;
        $this->vital_signs = $note->vital_signs ?? EvolutionNote::vitalSignsDefaults();
        $this->plan = $note->plan;
        $this->treatment = $note->treatment;
        $this->additional_notes = $note->additional_notes;
        $this->comments = $note->comments;
        $this->showForm = true;
    }

    public function deleteNote($id)
    {
        $note = EvolutionNote::find($id);
        if ($note) {

            $note->delete();
        }
        $this->loadData();
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->editingNoteId = null;
        $this->date = date('Y-m-d');
        $this->session_number = (collect($this->notes)->max('session_number') ?: 0) + 1;
        $this->description = null;
        $this->vital_signs = EvolutionNote::vitalSignsDefaults();
        $this->plan = null;
        $this->treatment = null;
        $this->additional_notes = null;
        $this->comments = null;
    }

    public function render()
    {
        return view('livewire.evolution-notes');
    }
}

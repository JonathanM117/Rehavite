<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ClinicalScale;
use App\Models\Consultation;

class ConsultationScales extends Component
{
    public $consultationId;
    public $scales = [];
    public $showForm = false;
    public $scaleType = 'tinetti'; // tinetti, borg_6mwt
    public $editingScaleId = null;
    public $date = '';

    // Tinetti items
    public $tinetti_balance = [
        'sitting' => 1,
        'rising' => 2,
        'attempts' => 2,
        'immediate_standing' => 2,
        'standing_balance' => 2,
        'nudged' => 2,
        'eyes_closed' => 1,
        'turning' => 2,
        'sitting_down' => 2,
    ];
    public $tinetti_gait = [
        'initiation' => 1,
        'step_length' => 2,
        'step_height' => 2,
        'step_symmetry' => 1,
        'step_continuity' => 1,
        'path' => 2,
        'trunk' => 2,
        'walking_stance' => 1,
    ];
    public $tinetti_notes = '';

    // Borg / 6MWT items
    public $borg_initial = 0;
    public $borg_final = 0;
    public $distance_meters = 0;
    public $vital_signs = [
        'hr_start' => '',
        'hr_end' => '',
        'spo2_start' => '',
        'spo2_end' => '',
        'bp_start' => '',
        'bp_end' => '',
    ];
    public $borg_notes = '';

    public function mount($consultationId)
    {
        $this->consultationId = $consultationId;
        $this->date = date('Y-m-d');
        $this->loadScales();
    }

    public function loadScales()
    {
        $this->scales = ClinicalScale::where('consultation_id', $this->consultationId)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if ($this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->editingScaleId = null;
        $this->date = date('Y-m-d');
        $this->tinetti_notes = '';
        $this->borg_notes = '';
        $this->distance_meters = 0;
        $this->borg_initial = 0;
        $this->borg_final = 0;
        $this->vital_signs = [
            'hr_start' => '',
            'hr_end' => '',
            'spo2_start' => '',
            'spo2_end' => '',
            'bp_start' => '',
            'bp_end' => '',
        ];
    }

    public function saveScale()
    {
        $this->validate([
            'date' => 'required|date',
            'scaleType' => 'required|in:tinetti,borg_6mwt',
        ]);

        // Enforce 48-hour consultation lock for editing/adding scales
        $consultation = Consultation::find($this->consultationId);
        if ($consultation && !auth()->user()->admin && $consultation->created_at && $consultation->created_at->diffInHours(now()) > 48) {
            session()->flash('error', 'No se pueden modificar ni añadir escalas en una consulta de más de 48 horas.');
            return;
        }

        $data = [];
        if ($this->scaleType === 'tinetti') {
            $balanceScore = array_sum($this->tinetti_balance);
            $gaitScore = array_sum($this->tinetti_gait);
            $data = [
                'balance' => $this->tinetti_balance,
                'gait' => $this->tinetti_gait,
                'balance_score' => $balanceScore,
                'gait_score' => $gaitScore,
                'total_score' => $balanceScore + $gaitScore,
                'notes' => $this->tinetti_notes,
            ];
        } else {
            $data = [
                'borg_initial' => $this->borg_initial,
                'borg_final' => $this->borg_final,
                'distance_meters' => $this->distance_meters,
                'vital_signs' => $this->vital_signs,
                'notes' => $this->borg_notes,
            ];
        }

        if ($this->editingScaleId) {
            $scale = ClinicalScale::find($this->editingScaleId);
            if ($scale) {
                $scale->update([
                    'date' => $this->date,
                    'data' => $data,
                ]);
            }
        } else {
            ClinicalScale::create([
                'consultation_id' => $this->consultationId,
                'type' => $this->scaleType,
                'date' => $this->date,
                'data' => $data,
            ]);
        }

        session()->flash('success', 'Escala guardada con éxito.');
        $this->showForm = false;
        $this->resetForm();
        $this->loadScales();
    }

    public function editScale($id)
    {
        $scale = ClinicalScale::find($id);
        if (!$scale) return;

        // Enforce 48-hour consultation lock
        $consultation = Consultation::find($this->consultationId);
        if ($consultation && !auth()->user()->admin && $consultation->created_at && $consultation->created_at->diffInHours(now()) > 48) {
            session()->flash('error', 'No se pueden modificar escalas en una consulta de más de 48 horas.');
            return;
        }

        $this->editingScaleId = $scale->id;
        $this->scaleType = $scale->type;
        $this->date = $scale->date->format('Y-m-d');

        if ($scale->type === 'tinetti') {
            $this->tinetti_balance = $scale->data['balance'] ?? $this->tinetti_balance;
            $this->tinetti_gait = $scale->data['gait'] ?? $this->tinetti_gait;
            $this->tinetti_notes = $scale->data['notes'] ?? '';
        } else {
            $this->borg_initial = $scale->data['borg_initial'] ?? 0;
            $this->borg_final = $scale->data['borg_final'] ?? 0;
            $this->distance_meters = $scale->data['distance_meters'] ?? 0;
            $this->vital_signs = $scale->data['vital_signs'] ?? $this->vital_signs;
            $this->borg_notes = $scale->data['notes'] ?? '';
        }

        $this->showForm = true;
    }

    public function deleteScale($id)
    {
        // Enforce 48-hour consultation lock
        $consultation = Consultation::find($this->consultationId);
        if ($consultation && !auth()->user()->admin && $consultation->created_at && $consultation->created_at->diffInHours(now()) > 48) {
            session()->flash('error', 'No se pueden eliminar escalas en una consulta de más de 48 horas.');
            return;
        }

        $scale = ClinicalScale::find($id);
        if ($scale) {
            $scale->delete();
            session()->flash('success', 'Escala eliminada.');
        }
        $this->loadScales();
    }

    public function render()
    {
        return view('livewire.consultation-scales');
    }
}

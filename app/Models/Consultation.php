<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exercise;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'reason',
        'current_condition',
        'previous_studies_notes',
        'physical_exam',
        'diagnosis',
        'prognosis',
        'treatment_objectives',
        'discharge_recommendations',
        'discharge_reason',
        'status',
    ];

    protected $casts = [
        'current_condition' => 'array',
        'physical_exam' => 'array',
    ];

    // ─── Relationships ───────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function evolutionNotes()
    {
        return $this->hasMany(EvolutionNote::class)->orderBy('date', 'desc');
    }

    public function studies()
    {
        return $this->hasMany(Study::class)->orderBy('date', 'desc');
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class)->orderBy('date', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helpers ─────────────────────────────────────

    /**
     * Default structure for current_condition JSON field.
     */
    public static function currentConditionDefaults(): array
    {
        return [
            'inicio' => '',
            'mecanismo_lesion' => '',
            'evolucion' => '',
            'tratamiento_actual' => '',
            'limitacion_movimiento' => '',
            'eva' => '',
            'fuerza' => '',
            'actividad_fisica' => '',
            'comportamiento_sintomas' => '',
            'cambio_peso' => '',
            'cambios_apetito' => '',
            'debilidad_astenia' => '',
            'incontinencia' => '',
            'otros' => '',
        ];
    }

    /**
     * Default structure for physical_exam JSON field.
     */
    public static function physicalExamDefaults(): array
    {
        return [
            'temperatura' => '',
            'frecuencia_cardiaca' => '',
            'tension_arterial' => '',
            'frecuencia_respiratoria' => '',
            'saturacion_oxigeno' => '',
            'peso' => '',
            'altura' => '',
            'traslado' => '',
            'tipo_marcha' => '',
            'pruebas_complementarias' => '',
            'otros' => '',
        ];
    }
}

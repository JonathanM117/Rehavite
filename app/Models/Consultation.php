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
        'limitations_restrictions',
        'clinical_flags',
        'prognosis',
        'treatment_objectives',
        'discharge_recommendations',
        'discharge_reason',
        'status',
    ];

    protected $casts = [
        'current_condition' => 'array',
        'physical_exam' => 'array',
        'clinical_flags' => 'array',
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

    public function clinicalScales()
    {
        return $this->hasMany(ClinicalScale::class)->orderBy('date', 'desc');
    }

    public function clinicalPhotos()
    {
        return $this->hasMany(ClinicalPhoto::class)->orderBy('date', 'desc');
    }

    /**
     * Documentos clínicos del repositorio del paciente referenciados por este episodio.
     * El episodio no almacena el archivo, solo lo referencia.
     */
    public function clinicalDocuments()
    {
        return $this->belongsToMany(ClinicalDocument::class, 'clinical_document_consultation');
    }

    // ─── Helpers ─────────────────────────────────────

    public function getStatusLabelAttribute()
    {
        $labels = [
            'valoracion' => 'En valoración',
            'activo' => 'Activo',
            'seguimiento' => 'Seguimiento',
            'alta_clinica' => 'Alta clínica',
            'suspendido' => 'Suspendido',
            'abandono' => 'Abandono terapéutico',
            'referido' => 'Referido',
            'inactivo' => 'Expediente inactivo',
            // fallbacks
            'open' => 'Activo',
            'in_progress' => 'En curso',
            'closed' => 'Inactiva',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

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
            'tipo_dolor' => '',
            'comportamiento_sintomas' => '',
            'factores_alivian' => '',
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

    /**
     * Default structure for clinical_flags JSON field.
     */
    public static function clinicalFlagsDefaults(): array
    {
        return [
            'rojas' => [],
            'amarillas' => [],
            'azules' => [],
            'negras' => [],
            'blancas' => [],
            'verdes' => [],
            'observaciones' => '',
        ];
    }
}

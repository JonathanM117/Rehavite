<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'age',
        'marital_status',
        'address',
        'laterality',
        'occupation',
        'economic_dependency',
        'primary_caregiver',
        'guardian_name',
        'guardian_birth_date',
        'guardian_age',
        'guardian_gender',
        'observations',
        'family_history',
        'personal_non_pathological',
        'personal_pathological',
        'systems_review',
    ];

    protected $casts = [
        'guardian_birth_date' => 'date',
        'family_history' => 'array',
        'personal_non_pathological' => 'array',
        'personal_pathological' => 'array',
        'systems_review' => 'array',
    ];

    // ─── Relationships ───────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // ─── Helpers ─────────────────────────────────────

    /**
     * Default structure for family_history JSON field.
     */
    public static function familyHistoryDefaults(): array
    {
        return [
            'diabetes' => '',
            'cardiacas' => '',
            'vasculares' => '',
            'pulmonares' => '',
            'neoplasias' => '',
            'epilepsia' => '',
            'sindromes' => '',
            'sistema_nervioso' => '',
            'autoinmunes' => '',
            'otros' => '',
        ];
    }

    /**
     * Default structure for personal_non_pathological JSON field.
     */
    public static function personalNonPathologicalDefaults(): array
    {
        return [
            'lugar_nacimiento' => '',
            'residencia_actual' => '',
            'casa_habitacion' => '',
            'transporte' => '',
            'nivel_socioeconomico' => '',
            'animales_domesticos' => '',
            'dieta' => '',
            'avd' => '',
            'estado_salud' => '',
            'habitos_sueno' => '',
            'otros' => '',
        ];
    }

    /**
     * Default structure for personal_pathological JSON field.
     */
    public static function personalPathologicalDefaults(): array
    {
        return [
            'hospitalizacion' => '',
            'cirugias' => '',
            'habitos_toxicos' => '',
            'alergias' => '',
            'traumatismos' => '',
            'convulsiones' => '',
            'neoplasias' => '',
            'hipertension' => '',
            'diabetes' => '',
            'reumatologicos' => '',
            'psiquiatricos' => '',
            'enfermedades_congenitas' => '',
            'medicamentos_actuales' => '',
            'otros' => '',
        ];
    }

    /**
     * Default structure for systems_review JSON field.
     */
    public static function systemsReviewDefaults(): array
    {
        return [
            'visual' => '',
            'auditivo' => '',
            'sensorial' => '',
            'musculoesqueletico' => '',
            'sistema_nervioso' => '',
            'metabolico' => '',
            'respiratorio' => '',
            'cardiaco' => '',
            'gastrointestinal' => '',
            'genitourinario' => '',
            'piel' => '',
            'otros' => '',
        ];
    }
}

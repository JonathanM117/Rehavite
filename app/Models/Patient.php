<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Consultation;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'last_name',
        'email',
        'phone',
        'phone_own',
        'phone_confirmation',
        'birth_date',
        'gender',
        'diagnosis',
        'status',
        'deletion_requested_by',
        'deletion_requested_at',
        // Historia clínica (antes en la tabla medical_records, unificada aquí)
        'manual_age',
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
        'pediatric_history',
        // Rediseño del Expediente (adopción del HTML de referencia)
        'body_regions',
        'phone_owner',
        'alt_contact_name',
        'alt_contact_relationship',
        'alt_contact_phone',
        'referral_source',
        'referral_source_other',
        'referring_doctor',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'guardian_required',
        'birth_place',
        'street_address',
        'neighborhood',
        'municipality',
        'zone',
        'housing_type',
        'transportation',
        'architectural_barriers',
        'education_level',
        'physical_build',
        'stress_level',
        'work_demand',
        'diet',
        'adl_independence',
        'perceived_health',
        'sleep_habits',
        'pets',
        'non_pathological_notes',
        'medical_history_matrix',
        'medical_history_deny_family',
        'medical_history_deny_patient',
        'medical_history_notes',
        'symptoms_review',
        'toxic_habits',
        'allergies',
        'allergies_detail',
        'hospitalizations',
        'surgeries',
        'traumas',
        'current_medications',
        'events_notes',
    ];

    protected $casts = [
        'birth_date'             => 'date',
        'deletion_requested_at'  => 'datetime',
        'guardian_birth_date'    => 'date',
        'family_history'             => 'array',
        'personal_non_pathological'  => 'array',
        'personal_pathological'      => 'array',
        'systems_review'              => 'array',
        'pediatric_history'           => 'array',
        'body_regions'                => 'array',
        'architectural_barriers'      => 'array',
        'pets'                        => 'array',
        'medical_history_matrix'      => 'array',
        'medical_history_deny_family' => 'boolean',
        'medical_history_deny_patient' => 'boolean',
        'symptoms_review'             => 'array',
        'toxic_habits'                => 'array',
        'allergies'                   => 'array',
        'hospitalizations'            => 'array',
        'surgeries'                   => 'array',
        'traumas'                     => 'array',
        'current_medications'         => 'array',
        'guardian_required'           => 'boolean',
    ];

    // ─── Status Helpers ──────────────────────────────

    public function isPendingDeletion(): bool
    {
        return $this->status === 'pending_deletion';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    // ─── Relationships ───────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deletionRequestedBy()
    {
        return $this->belongsTo(User::class, 'deletion_requested_by');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function clinicalDocuments()
    {
        return $this->hasMany(ClinicalDocument::class)->orderBy('date', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function clinicalPhotos()
    {
        return $this->hasMany(ClinicalPhoto::class)->orderBy('date', 'desc');
    }

    /**
     * Physiotherapists who have been granted collaborator access to this patient.
     */
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'patient_collaborators')
                    ->withPivot('granted_by')
                    ->withTimestamps();
    }

    /**
     * Audit logs related to this patient.
     */
    public function auditLogs()
    {
        return AuditLog::where('model_type', self::class)
                       ->where('model_id', $this->id)
                       ->orWhere(function ($q) {
                           // Also include consultation-level logs for this patient
                           $q->where('model_type', Consultation::class)
                             ->whereIn('model_id', $this->consultations()->pluck('id'));
                       })
                       ->orderByDesc('created_at');
    }

    /**
     * Check if a user can access this patient (owner, collaborator, or admin).
     */
    public function canBeAccessedBy(User $user): bool
    {
        if ($user->admin) return true;
        if ($this->user_id === $user->id) return true;
        return $this->collaborators()->where('user_id', $user->id)->exists();
    }

    // ─── Accessors ───────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function getAgeLabelAttribute(): string
    {
        if (!$this->birth_date) {
            return 'No especificada';
        }

        $now = now();
        $diff = $this->birth_date->diff($now);

        if ($diff->y >= 1) {
            return $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
        }

        // Less than a year: show months and days
        $months = $diff->m;
        $days = $diff->d;

        $label = '';
        if ($months > 0) {
            $label .= $months . ' ' . ($months === 1 ? 'mes' : 'meses');
        }
        if ($days > 0) {
            if ($label !== '') {
                $label .= ' y ';
            }
            $label .= $days . ' ' . ($days === 1 ? 'día' : 'días');
        }

        if ($label === '') {
            return 'Recién nacido';
        }

        return $label;
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'activo' => 'Activo',
            'active' => 'Activo',
            'seguimiento' => 'Seguimiento',
            'alta_clinica' => 'Alta clínica',
            'discharged' => 'Alta clínica',
            'suspendido' => 'Suspendido',
            'abandono' => 'Abandono terapéutico',
            'abandono_terapeutico' => 'Abandono terapéutico',
            'referido' => 'Referido',
            'inactive' => 'Inactivo',
            'pending_deletion' => 'Pendiente de eliminación',
            'archived' => 'Archivado',
        ];

        return $labels[strtolower($this->status)] ?? ucfirst(str_replace('_', ' ', $this->status ?? ''));
    }

    public function getGenderLabelAttribute(): string
    {
        $labels = [
            'masculino' => 'Masculino',
            'femenino' => 'Femenino',
            'otro' => 'Otro',
            'no_especificado' => 'Prefiere no especificar',
        ];

        return $labels[$this->gender] ?? '—';
    }

    public function getExpedienteIdAttribute(): string
    {
        $nombre = \Illuminate\Support\Str::ascii(trim($this->name));
        $apellidos = \Illuminate\Support\Str::ascii(trim($this->last_name));
        $fecha = $this->birth_date ? $this->birth_date->format('dmY') : '00000000';

        $nombreParts = preg_split('/\s+/', $nombre);
        $apellidoParts = preg_split('/\s+/', $apellidos);

        // 3 letras del primer nombre + 2 del primer apellido
        $letrasNombre = substr($nombreParts[0] ?? $nombre, 0, 3);
        $letrasApellido = substr($apellidoParts[0] ?? '', 0, 2);

        $exp = strtoupper($letrasNombre . $letrasApellido);

        return $exp . '-' . $fecha;
    }

    // ─── Valores por defecto de la historia clínica (campos JSON) ───

    public static function pediatricHistoryDefaults(): array
    {
        return [
            'is_pediatric' => false,
            'gestational_age' => '',
            'birth_weight' => '',
            'birth_height' => '',
            'apgar' => '',
            'delivery_type' => '',
            'complications' => '',
            'neonatal_hospitalization' => '',
            'milestone_head_control' => '',
            'milestone_sitting' => '',
            'milestone_crawling' => '',
            'milestone_standing' => '',
            'milestone_walking' => '',
            'language_development' => '',
            'vaccinations' => '',
            'reflexes' => '',
            'other_development' => '',
        ];
    }

    public static function familyHistoryDefaults(): array
    {
        return [
            'diabetes' => '',
            'cardiacas' => '',
            'vasculares' => '',
            'pulmonares' => '',
            'neoplasias' => '',
            'metabolicos' => '',
            'sindromes' => '',
            'sistema_nervioso' => '',
            'autoinmunes' => '',
            'otros' => '',
        ];
    }

    public static function personalNonPathologicalDefaults(): array
    {
        return [
            'lugar_nacimiento' => '',
            'residencia_actual' => '',
            'casa_habitacion' => '',
            'transporte' => '',
            'nivel_socioeconomico' => '',
            'animales_domesticos' => '',
            'complexion_fisica' => '',
            'nivel_estres' => '',
            'exigencia_laboral' => '',
            'dieta' => '',
            'avd' => '',
            'estado_salud' => '',
            'habitos_sueno' => '',
            'otros' => '',
        ];
    }

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

    /**
     * Fila vacía de la matriz de antecedentes (familia/paciente) para cada
     * condición del catálogo de \App\Support\ExpedienteCatalogo::antecedentes().
     */
    public static function medicalHistoryMatrixDefaults(): array
    {
        $row = ['fam' => false, 'fam_detalle' => '', 'pac' => false, 'pac_detalle' => ''];

        return array_fill_keys(array_keys(\App\Support\ExpedienteCatalogo::antecedentes()), $row);
    }

    /**
     * Estado vacío del interrogatorio por síntomas (uno por cada clave de
     * \App\Support\ExpedienteCatalogo::interrogatorio()).
     */
    public static function symptomsReviewDefaults(): array
    {
        $row = ['presente' => null, 'detalle' => ''];

        return array_fill_keys(array_keys(\App\Support\ExpedienteCatalogo::interrogatorio()), $row);
    }
}

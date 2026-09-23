<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalDocument extends Model
{
    use HasFactory;

    const TYPES = [
        'Consentimiento informado',
        'Informe médico',
        'Receta médica',
        'Interconsulta / Referencia',
        'Incapacidad médica',
        'Documento administrativo',
        'Otro',
    ];

    /**
     * Estados posibles de un documento: vigente, actualizar, historico.
     */
    const STATUS_LABELS = [
        'vigente' => 'Vigente',
        'actualizar' => 'Actualizar',
        'historico' => 'Histórico',
    ];

    const STATUS_EMOJIS = [
        'vigente' => '🟢',
        'actualizar' => '🟡',
        'historico' => '⚪',
    ];

    protected $fillable = [
        'patient_id',
        'type',
        'date',
        'description',
        'file_path',
        'manage_status_manually',
        'manual_status',
        'valid_until',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'manage_status_manually' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Episodios de atención (consultas) que referencian este documento.
     * El documento se guarda una sola vez en el repositorio del paciente;
     * un episodio solo lo referencia, no lo almacena de nuevo.
     */
    public function consultations()
    {
        return $this->belongsToMany(Consultation::class, 'clinical_document_consultation');
    }

    // ─── Estado del documento ────────────────────────

    public function getStatusAttribute(): string
    {
        if ($this->manage_status_manually) {
            return $this->manual_status ?: 'vigente';
        }

        return match ($this->type) {
            'Consentimiento informado' => $this->date->copy()->addMonths(12)->isFuture() ? 'vigente' : 'historico',

            'Informe médico', 'Documento administrativo' => $this->hasNewerOfSameType() ? 'historico' : 'vigente',

            'Receta médica' => $this->hasNewerOfSameType()
                ? 'historico'
                : ($this->treatmentHasEnded() ? 'actualizar' : 'vigente'),

            'Interconsulta / Referencia' => 'historico',

            'Incapacidad médica' => (!$this->valid_until || $this->valid_until->isFuture() || $this->valid_until->isToday())
                ? 'vigente'
                : 'historico',

            default => $this->manual_status ?: 'vigente', // 'Otro' y cualquier tipo no contemplado: manual
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Vigente';
    }

    public function getStatusEmojiAttribute(): string
    {
        return self::STATUS_EMOJIS[$this->status] ?? '🟢';
    }

    protected function hasNewerOfSameType(): bool
    {
        return self::where('patient_id', $this->patient_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->where('date', '>', $this->date)
            ->exists();
    }

    protected function treatmentHasEnded(): bool
    {
        $finishedStatuses = ['alta_clinica', 'discharged', 'abandono', 'abandono_terapeutico', 'suspendido', 'referido', 'inactive'];
        return in_array($this->patient?->status, $finishedStatuses);
    }
}

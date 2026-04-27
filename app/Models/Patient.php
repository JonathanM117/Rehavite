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
        'birth_date',
        'gender',
        'diagnosis',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // ─── Relationships ───────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
}

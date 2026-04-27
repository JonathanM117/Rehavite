<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvolutionNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'date',
        'session_number',
        'description',
        'vital_signs',
        'plan',
        'treatment',
        'additional_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'vital_signs' => 'array',
    ];

    // ─── Relationships ───────────────────────────────

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    // ─── Helpers ─────────────────────────────────────

    /**
     * Default structure for vital_signs JSON field.
     */
    public static function vitalSignsDefaults(): array
    {
        return [
            'temp_start' => '',
            'temp_end' => '',
            'saturation_start' => '',
            'saturation_end' => '',
            'bp_start' => '',
            'bp_end' => '',
            'hr_start' => '',
            'hr_end' => '',
        ];
    }
}

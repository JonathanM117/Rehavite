<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'date',
        'file_path',
        'description',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ─── Relationships ───────────────────────────────

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}

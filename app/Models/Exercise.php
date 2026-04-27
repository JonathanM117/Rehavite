<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'date',
        'file_path',
        'description',
        'title',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'type',
        'data',
        'date',
    ];

    protected $casts = [
        'data' => 'array',
        'date' => 'date',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}

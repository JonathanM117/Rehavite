<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'consultation_id',
        'concept',
        'session_number',
        'date',
        'payment_method',
        'total_amount',
        'amount_paid',
        'balance',
        'therapist',
        'invoice_number',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class)->orderBy('date');
    }

    // Total paid across all installments
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->installments->sum('amount') + (float) $this->amount_paid;
    }

    // Remaining balance considering installments
    public function getRemainingBalanceAttribute(): float
    {
        $extra = (float) $this->installments->sum('amount');
        return max(0, (float) $this->balance - $extra);
    }
}

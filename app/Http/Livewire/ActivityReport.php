<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Patient;
use Carbon\Carbon;

class ActivityReport extends Component
{
    public $period = 'month'; // month, week, all

    public function render()
    {
        $startDate = match($this->period) {
            'week'  => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => null,
        };

        $therapists = User::orderBy('admin', 'desc')->orderBy('name')->get()->map(function ($user) use ($startDate) {
            $consultationQuery = Consultation::where('user_id', $user->id);
            $paymentQuery      = Payment::where('user_id', $user->id);

            if ($startDate) {
                $consultationQuery->where('created_at', '>=', $startDate);
                $paymentQuery->where('created_at', '>=', $startDate);
            }

            $user->total_patients      = Patient::where('user_id', $user->id)->count();
            $user->period_consultations = $consultationQuery->count();
            $user->period_payments     = $paymentQuery->count();
            $user->total_billed        = Payment::where('user_id', $user->id)
                ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                ->sum('amount_paid');
            $user->last_activity       = Consultation::where('user_id', $user->id)
                ->latest('created_at')->value('created_at');

            return $user;
        });

        // Global clinic stats
        $stats = [
            'total_patients'      => Patient::count(),
            'total_consultations' => $startDate ? Consultation::where('created_at', '>=', $startDate)->count() : Consultation::count(),
            'total_payments'      => $startDate ? Payment::where('created_at', '>=', $startDate)->sum('amount_paid') : Payment::sum('amount_paid'),
            'active_therapists'   => User::count(),
        ];

        return view('livewire.activity-report', compact('therapists', 'stats'));
    }
}

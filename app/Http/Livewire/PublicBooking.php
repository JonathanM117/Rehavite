<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Appointment;
use App\Models\TherapistSchedule;
use Carbon\Carbon;

class PublicBooking extends Component
{
    public $therapists;
    public $selectedTherapistId = '';
    public $selectedDate = '';
    public $selectedTimeSlot = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $notes = '';
    
    public $availableSlots = [];
    public $bookingSuccess = false;
    public $createdAppointment = null;

    public function mount()
    {
        // Get all physiotherapists
        $this->therapists = User::orderBy('name')->get();
        $this->selectedDate = date('Y-m-d');
    }

    public function updatedSelectedTherapistId()
    {
        $this->selectedTimeSlot = '';
        $this->calculateAvailableSlots();
    }

    public function updatedSelectedDate()
    {
        $this->selectedTimeSlot = '';
        $this->calculateAvailableSlots();
    }

    public function calculateAvailableSlots()
    {
        if (!$this->selectedTherapistId || !$this->selectedDate) {
            $this->availableSlots = [];
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        
        // Block past dates
        if ($date->isPast() && !$date->isToday()) {
            $this->availableSlots = [];
            return;
        }

        $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Fetch custom schedule
        $schedule = TherapistSchedule::where('user_id', $this->selectedTherapistId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        // Fallback default working hours
        $startStr = '09:00:00';
        $endStr = '18:00:00';
        
        if ($schedule) {
            $startStr = $schedule->start_time;
            $endStr = $schedule->end_time;
        } else {
            // Default Sundays: closed
            if ($dayOfWeek === 0) {
                $this->availableSlots = [];
                return;
            }
            // Default Saturdays: half day
            if ($dayOfWeek === 6) {
                $endStr = '14:00:00';
            }
        }

        $start = Carbon::parse($this->selectedDate . ' ' . $startStr);
        $end = Carbon::parse($this->selectedDate . ' ' . $endStr);

        // Fetch booked appointments on that day
        $bookedTimes = Appointment::where('user_id', $this->selectedTherapistId)
            ->whereDate('start_time', $this->selectedDate)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $slotTime = $start->copy();

        while ($slotTime->lt($end)) {
            $slotEnd = $slotTime->copy()->addHour();
            
            // Do not suggest past hours if booking for today
            if ($date->isToday() && $slotTime->lt(now())) {
                $slotTime->addHour();
                continue;
            }

            // Check if slot overlaps with any booked appointment
            $isBooked = false;
            foreach ($bookedTimes as $booked) {
                if ($slotTime->lt($booked->end_time) && $slotEnd->gt($booked->start_time)) {
                    $isBooked = true;
                    break;
                }
            }

            if (!$isBooked) {
                $slots[] = $slotTime->format('H:i');
            }

            $slotTime->addHour();
        }

        $this->availableSlots = $slots;
    }

    public function bookAppointment()
    {
        $this->validate([
            'selectedTherapistId' => 'required|exists:users,id',
            'selectedDate' => 'required|date|after_or_equal:today',
            'selectedTimeSlot' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:10|max:20',
            'notes' => 'nullable|string|max:1000',
        ], [
            'selectedTherapistId.required' => 'Debes elegir un especialista.',
            'selectedTimeSlot.required' => 'Debes elegir un horario disponible.',
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'phone.required' => 'El teléfono es obligatorio.',
        ]);

        $start = Carbon::parse($this->selectedDate . ' ' . $this->selectedTimeSlot);
        $end = $start->copy()->addHour();

        // Final double check for overlap
        $exists = Appointment::where('user_id', $this->selectedTherapistId)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($start, $end) {
                $query->where(function($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
                });
            })->exists();

        if ($exists) {
            session()->flash('error', 'El horario seleccionado se acaba de ocupar. Por favor elige otro.');
            $this->calculateAvailableSlots();
            return;
        }

        // Check if there is an existing patient record matching this email/phone to link
        $matchingPatient = \App\Models\Patient::where('email', $this->email)
            ->orWhere('phone', $this->phone)
            ->first();

        $appointment = Appointment::create([
            'patient_id' => $matchingPatient?->id,
            'user_id' => $this->selectedTherapistId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        $this->createdAppointment = $appointment;
        $this->bookingSuccess = true;

        // Reset inputs
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->notes = '';
        $this->selectedTimeSlot = '';
        
        $this->calculateAvailableSlots();
    }

    public function render()
    {
        return view('livewire.public-booking')
            ->layout('layouts.app');
    }
}

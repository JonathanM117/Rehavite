<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Appointment;
use App\Models\TherapistSchedule;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AgendaManager extends Component
{
    use WithPagination;

    public $activeTab = 'appointments'; // appointments, schedules
    
    // Filters for appointments
    public $filterTherapist = '';
    public $filterStatus = '';
    public $filterDate = '';

    // Schedule editor fields (for logged-in therapist or chosen one)
    public $targetTherapistId = '';
    public $schedules = [];

    // Appointment creation modal/form fields
    public $showCreateModal = false;
    public $createName = '';
    public $createEmail = '';
    public $createPhone = '';
    public $createTherapistId = '';
    public $createDate = '';
    public $createTime = '';
    public $createNotes = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'activeTab' => ['except' => 'appointments'],
        'filterTherapist' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->admin) {
            $this->filterTherapist = $user->id;
            $this->targetTherapistId = $user->id;
            $this->createTherapistId = $user->id;
        } else {
            $firstTherapist = User::orderBy('name')->first();
            $this->targetTherapistId = $firstTherapist?->id ?? '';
            $this->createTherapistId = $firstTherapist?->id ?? '';
        }
        $this->filterDate = date('Y-m-d');
        $this->loadSchedules();
    }

    public function updatedTargetTherapistId()
    {
        $this->loadSchedules();
    }

    public function loadSchedules()
    {
        if (!$this->targetTherapistId) {
            $this->schedules = [];
            return;
        }

        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo',
        ];

        $scheduleList = [];

        foreach ($days as $dayNum => $dayName) {
            $s = TherapistSchedule::where('user_id', $this->targetTherapistId)
                ->where('day_of_week', $dayNum)
                ->first();

            $scheduleList[$dayNum] = [
                'day_name' => $dayName,
                'is_active' => $s ? (bool)$s->is_active : ($dayNum !== 0), // Sundays inactive by default
                'start_time' => $s ? substr($s->start_time, 0, 5) : '09:00',
                'end_time' => $s ? substr($s->end_time, 0, 5) : ($dayNum === 6 ? '14:00' : '18:00'),
            ];
        }

        $this->schedules = $scheduleList;
    }

    public function saveSchedules()
    {
        $user = Auth::user();
        // Only allow admins to update other therapists' schedules
        $therapistId = $user->admin ? $this->targetTherapistId : $user->id;

        if (!$therapistId) return;

        foreach ($this->schedules as $dayNum => $data) {
            TherapistSchedule::updateOrCreate([
                'user_id' => $therapistId,
                'day_of_week' => $dayNum,
            ], [
                'start_time' => $data['start_time'] . ':00',
                'end_time' => $data['end_time'] . ':00',
                'is_active' => $data['is_active'],
            ]);
        }

        session()->flash('success', 'Horarios de atención actualizados con éxito.');
    }

    public function confirmAppointment($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->update(['status' => 'confirmed']);
            
            // Link to actual patient if not linked yet
            if (!$appointment->patient_id) {
                $matchingPatient = \App\Models\Patient::where('email', $appointment->email)
                    ->orWhere('phone', $appointment->phone)
                    ->first();
                if ($matchingPatient) {
                    $appointment->update(['patient_id' => $matchingPatient->id]);
                }
            }

            session()->flash('success', 'Cita confirmada correctamente.');
        }
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->update(['status' => 'cancelled']);
            session()->flash('success', 'Cita cancelada correctamente.');
        }
    }

    public function openCreateModal()
    {
        $this->createName = '';
        $this->createEmail = '';
        $this->createPhone = '';
        $this->createNotes = '';
        $this->createDate = date('Y-m-d');
        $this->createTime = '09:00';
        $this->showCreateModal = true;
    }

    public function createAppointment()
    {
        $user = Auth::user();
        $therapistId = $user->admin ? $this->createTherapistId : $user->id;

        $this->validate([
            'createName' => 'required|string|max:255',
            'createEmail' => 'required|email|max:255',
            'createPhone' => 'required|string|max:20',
            'createDate' => 'required|date',
            'createTime' => 'required',
            'createNotes' => 'nullable|string|max:1000',
        ], [
            'createName.required' => 'El nombre es obligatorio.',
            'createEmail.required' => 'El correo es obligatorio.',
            'createPhone.required' => 'El teléfono es obligatorio.',
            'createDate.required' => 'La fecha es obligatoria.',
            'createTime.required' => 'El horario es obligatorio.',
        ]);

        $start = Carbon::parse($this->createDate . ' ' . $this->createTime);
        $end = $start->copy()->addHour();

        $matchingPatient = \App\Models\Patient::where('email', $this->createEmail)
            ->orWhere('phone', $this->createPhone)
            ->first();

        Appointment::create([
            'patient_id' => $matchingPatient?->id,
            'user_id' => $therapistId,
            'name' => $this->createName,
            'email' => $this->createEmail,
            'phone' => $this->createPhone,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'confirmed', // Created by staff/therapist is confirmed by default
            'notes' => $this->createNotes,
        ]);

        $this->showCreateModal = false;
        session()->flash('success', 'Cita creada y agendada correctamente.');
    }

    public function getWhatsAppLink($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            return WhatsAppService::generateConfirmationLink($appointment);
        }
        return '';
    }

    public function render()
    {
        $user = Auth::user();

        $appointmentsQuery = Appointment::with(['user', 'patient']);

        // Data Isolation: non-admins only see their own appointments
        if (!$user->admin) {
            $appointmentsQuery->where('user_id', $user->id);
        } else {
            $appointmentsQuery->when($this->filterTherapist, function ($query) {
                $query->where('user_id', $this->filterTherapist);
            });
        }

        $appointmentsQuery->when($this->filterStatus, function ($query) {
            $query->where('status', $this->filterStatus);
        })
        ->when($this->filterDate, function ($query) {
            $query->whereDate('start_time', $this->filterDate);
        });

        $appointments = $appointmentsQuery->orderBy('start_time', 'asc')->paginate(20);
        $therapists = User::orderBy('name')->get();

        return view('livewire.agenda-manager', [
            'appointments' => $appointments,
            'therapists' => $therapists,
        ])
        ->extends('layouts.admin')
        ->section('content');
    }
}

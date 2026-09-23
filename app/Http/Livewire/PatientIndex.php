<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $birthdayFilter = false;
    public $therapistFilter = '';
    public $birthDateStart = '';
    public $birthDateEnd = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'birthdayFilter' => ['except' => false],
        'therapistFilter' => ['except' => ''],
        'birthDateStart' => ['except' => ''],
        'birthDateEnd' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['therapistFilter', 'statusFilter', 'birthDateStart', 'birthDateEnd']);
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $user = Auth::user();

        $patientsQuery = Patient::select('patients.*')
            ->with('user')
            ->selectSub(function ($query) {
                $query->select('name')
                    ->from('users')
                    ->whereColumn('users.id', 'patients.user_id')
                    ->limit(1);
            }, 'therapist_name')
            ->selectSub(function ($query) {
                $query->select('created_at')
                    ->from('audit_logs')
                    ->where(function($q) {
                        $q->where('model_type', 'App\Models\Patient')
                          ->whereColumn('model_id', 'patients.id')
                          ->orWhere(function($q2) {
                              $q2->where('model_type', 'App\Models\Consultation')
                                 ->whereIn('model_id', function($sub) {
                                     $sub->select('id')
                                         ->from('consultations')
                                         ->whereColumn('patient_id', 'patients.id');
                                 });
                          });
                    })
                    ->orderByDesc('created_at')
                    ->limit(1);
            }, 'last_activity_date');

        // Data Isolation: Fisioterapeutas see assigned + collaborated patients
        $patientsQuery->when(!$user->admin, function ($query) use ($user) {
            return $query->where(function($q) use ($user) {
                $q->where('patients.user_id', $user->id)
                  ->orWhereHas('collaborators', function($q2) use ($user) {
                      $q2->where('user_id', $user->id);
                  });
            });
        })
        // Admin filter by therapist
        ->when($user->admin && $this->therapistFilter, function ($query) {
            $query->where('patients.user_id', $this->therapistFilter);
        })
        // Filter by status
        ->when($this->statusFilter, function ($query) {
            $query->where('patients.status', $this->statusFilter);
        }, function ($query) {
            $query->whereNotIn('patients.status', ['archived']);
        })
        // Search logic
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('patients.name', 'LIKE', "%{$this->search}%")
                  ->orWhere('patients.last_name', 'LIKE', "%{$this->search}%")
                  ->orWhere('patients.diagnosis', 'LIKE', "%{$this->search}%")
                  ->orWhere('patients.email', 'LIKE', "%{$this->search}%")
                  ->orWhere('patients.phone', 'LIKE', "%{$this->search}%")
                  ->orWhereHas('user', function($q2) {
                      $q2->where('name', 'LIKE', "%{$this->search}%");
                  });
            });
        })
        // Birthday filter: shows patients born this month
        ->when($this->birthdayFilter, function ($query) {
            $query->whereMonth('patients.birth_date', now()->month);
        })
        // Birth date start range filter
        ->when($this->birthDateStart, function ($query) {
            $query->whereDate('patients.birth_date', '>=', $this->birthDateStart);
        })
        // Birth date end range filter
        ->when($this->birthDateEnd, function ($query) {
            $query->whereDate('patients.birth_date', '<=', $this->birthDateEnd);
        });

        // Apply Sorting
        if ($this->sortBy === 'therapist') {
            $patientsQuery->orderBy('therapist_name', $this->sortDirection);
        } elseif ($this->sortBy === 'last_activity') {
            $patientsQuery->orderBy('last_activity_date', $this->sortDirection);
        } else {
            // Guard default fields to avoid ambiguity
            $sortColumn = in_array($this->sortBy, ['id', 'name', 'birth_date', 'created_at']) ? 'patients.' . $this->sortBy : 'patients.created_at';
            $patientsQuery->orderBy($sortColumn, $this->sortDirection);
        }

        $patients = $patientsQuery->paginate(50);
        $therapists = $user->admin ? User::orderBy('name')->get() : collect();

        return view('livewire.patient-index', [
            'patients' => $patients,
            'therapists' => $therapists,
        ]);
    }
}

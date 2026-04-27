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

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'birthdayFilter' => ['except' => false],
        'therapistFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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

        $patients = Patient::with('user')
            // Data Isolation: Fisioterapeutas see assigned + collaborated patients
            ->when(!$user->admin, function ($query) use ($user) {
                return $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('collaborators', function($q2) use ($user) {
                          $q2->where('user_id', $user->id);
                      });
                });
            })
            // Admin filter by therapist
            ->when($user->admin && $this->therapistFilter, function ($query) {
                $query->where('user_id', $this->therapistFilter);
            })
            // Search logic
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'LIKE', "%{$this->search}%")
                      ->orWhere('last_name', 'LIKE', "%{$this->search}%")
                      ->orWhere('diagnosis', 'LIKE', "%{$this->search}%")
                      ->orWhere('email', 'LIKE', "%{$this->search}%")
                      ->orWhere('phone', 'LIKE', "%{$this->search}%")
                      ->orWhereHas('user', function($q2) {
                          $q2->where('name', 'LIKE', "%{$this->search}%");
                      });
                });
            })
            // Birthday filter: shows patients born this month
            ->when($this->birthdayFilter, function ($query) {
                $query->whereMonth('birth_date', now()->month);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $therapists = $user->admin ? User::orderBy('name')->get() : collect();

        return view('livewire.patient-index', [
            'patients' => $patients,
            'therapists' => $therapists,
        ]);
    }
}

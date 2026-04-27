<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $showDeleteModal = false;
    public $userIdToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleAdmin($userId)
    {
        if (!Auth::user()->admin) return;

        $user = User::findOrFail($userId);
        
        // Prevent deleting the last admin if necessary, 
        // but for now, we just follow the user's logic.
        $user->admin = !$user->admin;
        $user->save();

        session()->flash('success', 'Rol de usuario actualizado.');
    }

    public function confirmDelete($id)
    {
        $this->userIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if (!Auth::user()->admin) return;

        $user = User::findOrFail($this->userIdToDelete);
        $user->delete();

        $this->showDeleteModal = false;
        $this->userIdToDelete = null;
        
        session()->flash('success', 'Usuario eliminado correctamente.');
    }

    public function render()
    {
        $users = User::query()
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->roleFilter !== '', function($query) {
                if ($this->roleFilter === 'admin') {
                    $query->where('admin', true);
                } else {
                    $query->where('admin', false);
                }
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.user-index', [
            'users' => $users
        ]);
    }
}

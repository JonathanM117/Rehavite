<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $photo;
    public $user;

    public function mount()
    {
        $this->user = auth()->user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function updatedPhoto()
    {
        $extension = strtolower($this->photo->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $allowedExtensions)) {
            $this->reset('photo');
            $this->dispatch('alert', message: 'Este formato de archivo (.' . $extension . ') no es compatible para previsualización. Por favor usa JPG, PNG o WEBP.');
        }
    }

    public function updateProfile()
    {
        $userId = $this->user->id;
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB Max
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'Solo se permiten formatos JPG, JPEG, PNG y WEBP.',
            'photo.max' => 'La imagen no debe superar los 2MB.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->photo) {
            // Delete old photo if exists
            if ($this->user->profile_photo_path) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
            }
            
            $path = $this->photo->store('avatars', 'public');
            $data['profile_photo_path'] = $path;
        }

        $this->user->update($data);
        $this->reset(['password', 'password_confirmation', 'photo']);
        
        session()->flash('success', 'Perfil actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}

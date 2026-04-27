<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'admin',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helpers ─────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return \Illuminate\Support\Facades\Storage::url($this->profile_photo_path);
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=FFFFFF&background=1e293b";
    }

    // ─── AdminLTE Integration (Legacy) ───────────────

    public function adminlte_image(): string
    {
        return $this->avatar_url;
    }

    public function adminlte_desc(): string
    {
        return $this->admin ? 'Administrador' : 'Fisioterapeuta';
    }

    public function adminlte_profile_url(): string
    {
        return 'profile/username';
    }
}

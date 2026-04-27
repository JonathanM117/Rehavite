<div class="user-profile-container">
    <div class="flex-between mb-24">
        <div>
            <h2 style="margin:0;font-size:24px;font-weight:700;color:var(--text-primary);">Mi Perfil</h2>
            <p style="margin:4px 0 0 0;font-size:14px;color:var(--text-secondary);">Gestiona tu información personal y foto de perfil.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-modern alert-success mb-24">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-3" style="gap:24px;">
        {{-- Profile Preview Card --}}
        <div class="glass-card" style="height: fit-content; text-align: center; padding: 32px;">
            <div style="position:relative; width:120px; height:120px; margin:0 auto 24px;" 
                 x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false; alert('Error al subir la imagen. Asegúrate de que sea un formato válido (JPG, PNG, WEBP) y no supere los 2MB.')"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <div style="width:100%; height:100%; border-radius:50%; border:4px solid var(--accent-primary); padding:4px; background: rgba(0,0,0,0.2); overflow:hidden;">
                    @if ($photo && in_array(strtolower($photo->extension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                        <img src="{{ $photo->temporaryUrl() }}" style="width:100%; height:100%; object-fit: cover; border-radius:50%;">
                    @else
                        <img src="{{ $user->avatar_url }}" style="width:100%; height:100%; object-fit: cover; border-radius:50%;">
                    @endif
                </div>
                <label for="photo-upload" style="position:absolute; bottom:0; right:0; width:36px; height:36px; border-radius:50%; background:var(--accent-primary); color:white; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s;">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="photo-upload" wire:model="photo" style="display:none;" accept="image/*">
                </label>
            </div>
            
            {{-- Global Alert Script --}}
            <script>
                window.addEventListener('alert', event => {
                    alert(event.detail.message);
                });
            </script>
            
            <h3 style="margin:0; font-size:20px; font-weight:700;">{{ $user->name }}</h3>
            <p style="margin:8px 0 16px; font-size:14px; color:var(--text-muted);">{{ $user->email }}</p>
            
            <div class="badge-modern {{ $user->admin ? 'badge-info' : 'badge-success' }}" style="display:inline-block;">
                {{ $user->admin ? 'Administrador' : 'Fisioterapeuta' }}
            </div>

            <div wire:loading wire:target="photo" style="margin-top:16px; font-size:12px; color:var(--accent-primary);">
                <i class="fas fa-spinner fa-spin"></i> Procesando imagen...
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="glass-card grid-col-2" style="padding: 32px;">
            <form wire:submit.prevent="updateProfile">
                <div class="grid grid-2" style="gap:24px;">
                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" wire:model="name" class="form-control-modern" placeholder="Tu nombre...">
                        @error('name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" wire:model="email" class="form-control-modern" placeholder="email@ejemplo.com">
                        @error('email') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="margin: 24px 0; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 24px;">
                    <h4 style="margin:0 0 16px; font-size:16px; font-weight:600; color:var(--accent-primary);">Cambiar Contraseña</h4>
                    <p style="font-size:12px; color:var(--text-muted); margin-bottom:16px;">Deja los campos en blanco si no deseas cambiar tu contraseña.</p>
                    
                    <div class="grid grid-2" style="gap:24px;">
                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" wire:model="password" class="form-control-modern" placeholder="Mínimo 8 caracteres">
                            @error('password') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" wire:model="password_confirmation" class="form-control-modern" placeholder="Repite la contraseña">
                        </div>
                    </div>
                </div>

                <div style="text-align:right;">
                    <button type="submit" class="btn-modern btn-primary" wire:loading.attr="disabled" wire:target="updateProfile, photo">
                        <i class="fas fa-save" wire:loading.remove wire:target="updateProfile"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="updateProfile"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .grid-col-2 {
            grid-column: span 2;
        }
        @media (max-width: 991px) {
            .grid-col-2 {
                grid-column: span 3;
            }
        }
        label[for="photo-upload"]:hover {
            transform: scale(1.1);
        }
    </style>
</div>

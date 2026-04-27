<div>
    <div class="flex-between mb-24">
        <div>
            <h2 style="margin:0;font-size:24px;font-weight:700;color:var(--text-primary);">Gestión de Usuarios</h2>
            <p style="margin:4px 0 0 0;font-size:14px;color:var(--text-secondary);">Administración de fisioterapeutas y personal administrativo.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-modern btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Usuario
        </a>
    </div>

    @if (session('success'))
        <div class="alert-modern alert-success mb-24">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="glass-card mb-24" style="padding:16px;">
        <div class="grid grid-3" style="gap:16px; align-items: flex-end;">
            <div class="form-group mb-0">
                <label class="form-label" style="font-size:12px;">Buscar por nombre o email</label>
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" wire:model.live="search" class="form-control-modern" style="padding-left:36px;" placeholder="Ej: Juan Pérez...">
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label" style="font-size:12px;">Filtrar por Rol</label>
                <select wire:model.live="roleFilter" class="form-control-modern">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administradores</option>
                    <option value="therapist">Fisioterapeutas</option>
                </select>
            </div>
            <div style="text-align:right; font-size:13px; color:var(--text-muted); padding-bottom:10px;">
                Mostrando {{ $users->total() }} usuario(s)
            </div>
        </div>
    </div>

    {{-- Users Grid --}}
    <div class="grid grid-3 user-grid">
        @foreach($users as $user)
            <div class="glass-card user-card" wire:key="user-card-{{ $user->id }}" style="padding:0; overflow:hidden; position:relative; transition: transform 0.3s ease;">
                {{-- Admin Badge --}}
                @if($user->admin)
                    <div style="position:absolute; top:12px; right:12px; z-index:10;">
                        <span class="badge-modern badge-info" style="font-size:10px; padding:4px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">ADMIN</span>
                    </div>
                @endif

                {{-- Card Header with Avatar --}}
                <div style="padding:24px; text-align:center; border-bottom:1px solid rgba(255,255,255,0.05); background: linear-gradient(to bottom, rgba(255,255,255,0.02), transparent);">
                    <div style="width:80px; height:80px; border-radius:50%; margin:0 auto 16px; border:3px solid var(--accent-primary); padding:3px; background: rgba(0,0,0,0.2); overflow:hidden;">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit: cover; border-radius:50%;">
                    </div>
                    <h4 style="margin:0; font-size:16px; font-weight:600; color:var(--text-primary);">{{ $user->name }}</h4>
                    <p style="margin:4px 0 0 0; font-size:12px; color:var(--text-muted);">{{ $user->email }}</p>
                </div>

                {{-- Stats / Info --}}
                <div style="padding:16px; display:flex; justify-content:space-around; background: rgba(0,0,0,0.1);">
                    <div style="text-align:center;">
                        <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Pacientes</div>
                        <div style="font-size:16px; font-weight:700; color:var(--accent-primary);">{{ $user->patients_count ?? $user->patients->count() }}</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="padding:12px; display:flex; gap:8px;">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center;">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    
                    @if(auth()->id() !== $user->id)
                        <button wire:click="toggleAdmin({{ $user->id }})" class="btn-modern btn-sm" style="background:rgba(255,255,255,0.05); color:var(--text-primary); border:1px solid rgba(255,255,255,0.1); flex:none; width:40px; justify-content:center;" title="Cambiar permisos Admin">
                            <i class="fas fa-shield-alt"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $user->id }})" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); flex:none; width:40px; justify-content:center;">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:24px;">
        {{ $users->links() }}
    </div>

    {{-- Delete Modal Placeholder --}}
    @if($showDeleteModal)
    <div class="slide-panel-overlay" style="display:block; z-index:1000;" wire:click="$set('showDeleteModal', false)"></div>
    <div class="glass-card" style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1001; max-width:400px; width:90%; padding:32px; text-align:center;">
        <i class="fas fa-exclamation-triangle" style="font-size:48px; color:var(--danger); margin-bottom:16px;"></i>
        <h3 style="margin-bottom:12px;">¿Eliminar usuario?</h3>
        <p style="color:var(--text-secondary); margin-bottom:24px;">Esta acción no se puede deshacer. Se perderá el acceso de este fisioterapeuta al sistema.</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button class="btn-modern btn-secondary" wire:click="$set('showDeleteModal', false)">Cancelar</button>
            <button class="btn-modern" style="background:var(--danger); color:white;" wire:click="deleteUser">Eliminar para siempre</button>
        </div>
    </div>
    @endif

    <style>
        .user-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent-primary);
            box-shadow: 0 12px 24px rgba(0,0,0,0.4);
        }
        .user-grid {
            margin-top: 12px;
        }
    </style>
</div>

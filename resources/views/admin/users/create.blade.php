@extends('layouts.admin')

@section('title', 'Nuevo Usuario')
@section('page-title', 'Registrar Nuevo Fisioterapeuta')

@section('content')

    <div class="glass-card" style="max-width:800px; margin: 0 auto;">
        <div class="card-header-custom">
            <div class="card-title"><i class="fas fa-user-plus"></i> Datos del Usuario</div>
            <a href="{{ route('admin.users.index') }}" class="btn-modern btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-2" style="gap:24px;">
                <div class="form-group">
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" name="name" class="form-control-modern" value="{{ old('name') }}" placeholder="Ej: Dra. Elena Martínez" required>
                    @error('name') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="email" class="form-control-modern" value="{{ old('email') }}" placeholder="nombre@rehavite.com" required>
                    <small style="color:var(--text-muted); font-size:12px; margin-top:4px; display:block;">Solo se permiten correos @rehavite.com</small>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-2" style="gap:24px;">
                <div class="form-group">
                    <label class="form-label">Contraseña *</label>
                    <input type="password" name="password" class="form-control-modern" placeholder="Mínimo 8 caracteres" required>
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">¿Es Administrador?</label>
                    <div style="display:flex; align-items:center; gap:12px; margin-top:8px;">
                        <label class="modern-toggle">
                            <input type="checkbox" name="admin" value="1">
                            <span class="modern-slider"></span>
                        </label>
                        <span style="font-size:14px; color:var(--text-secondary);">Dar permisos de administración</span>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:32px; padding-top:24px; border-top:1px solid rgba(255,255,255,0.05);">
                <a href="{{ route('admin.users.index') }}" class="btn-modern btn-secondary">Cancelar</a>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save" style="margin-right:8px;"></i> Registrar Usuario
                </button>
            </div>
        </form>
    </div>

    <style>
        .modern-toggle {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .modern-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .modern-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255,255,255,0.1);
            transition: .4s;
            border-radius: 34px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .modern-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .modern-slider {
            background-color: var(--accent-primary);
        }
        input:checked + .modern-slider:before {
            transform: translateX(24px);
        }
    </style>

@endsection

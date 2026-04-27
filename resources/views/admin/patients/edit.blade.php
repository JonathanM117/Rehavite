@extends('layouts.admin')

@section('title', 'Editar Paciente')
@section('page-title', 'Editar Paciente')

@section('content')

    <div class="glass-card" style="max-width:800px;">
        <div class="card-header-custom">
            <div class="card-title"><i class="fas fa-user-edit"></i> Editar Datos</div>
            <a href="{{ route('admin.patients.show', $patient) }}" class="btn-modern btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al expediente
            </a>
        </div>

        <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control-modern" value="{{ old('name', $patient->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="last_name" class="form-control-modern" value="{{ old('last_name', $patient->last_name) }}" required>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Correo Electr&oacute;nico</label>
                    <input type="email" name="email" class="form-control-modern" value="{{ old('email', $patient->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tel&eacute;fono</label>
                    <input type="text" name="phone" class="form-control-modern" value="{{ old('phone', $patient->phone) }}">
                </div>
            </div>

            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="birth_date" class="form-control-modern" value="{{ old('birth_date', $patient->birth_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">G&eacute;nero *</label>
                    <select name="gender" class="form-control-modern" required>
                        <option value="no_especificado" {{ old('gender', $patient->gender) == 'no_especificado' ? 'selected' : '' }}>Prefiere no especificar</option>
                        <option value="femenino" {{ old('gender', $patient->gender) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="masculino" {{ old('gender', $patient->gender) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="otro" {{ old('gender', $patient->gender) == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-control-modern">
                        <option value="active" {{ old('status', $patient->status) == 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ old('status', $patient->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        <option value="discharged" {{ old('status', $patient->status) == 'discharged' ? 'selected' : '' }}>Alta</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Diagn&oacute;stico</label>
                <textarea name="diagnosis" class="form-control-modern" rows="3">{{ old('diagnosis', $patient->diagnosis) }}</textarea>
            </div>

            @if(auth()->user()->admin)
            <div class="form-group">
                <label class="form-label">Fisioterapeuta Asignado *</label>
                <select name="user_id" class="form-control-modern" required>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" {{ old('user_id', $patient->user_id) == $therapist->id ? 'selected' : '' }}>
                            {{ $therapist->name }} ({{ $therapist->admin ? 'Admin' : 'Fisio' }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(auth()->user()->admin)
            <div class="form-group">
                <label class="form-label">Fecha de Ingreso (Edici&oacute;n)</label>
                <input type="date" name="created_at" class="form-control-modern" value="{{ old('created_at', $patient->created_at->format('Y-m-d')) }}">
            </div>
            @endif

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn-modern btn-secondary">Cancelar</a>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

@endsection

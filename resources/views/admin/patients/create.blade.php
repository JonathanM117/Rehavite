@extends('layouts.admin')

@section('title', 'Nuevo Paciente')
@section('page-title', 'Registrar Nuevo Paciente')

@section('content')

    <div class="glass-card" style="max-width:800px;">
        <div class="card-header-custom">
            <div class="card-title"><i class="fas fa-user-plus"></i> Datos del Paciente</div>
            <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <form action="{{ route('admin.patients.store') }}" method="POST">
            @csrf

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control-modern" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="last_name" class="form-control-modern" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Correo Electr&oacute;nico</label>
                    <input type="email" name="email" class="form-control-modern" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tel&eacute;fono</label>
                    <input type="text" name="phone" class="form-control-modern" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="birth_date" class="form-control-modern" value="{{ old('birth_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">G&eacute;nero *</label>
                    <select name="gender" class="form-control-modern" required>
                        <option value="no_especificado" {{ old('gender') == 'no_especificado' ? 'selected' : '' }}>Prefiere no especificar</option>
                        <option value="femenino" {{ old('gender') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="masculino" {{ old('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="otro" {{ old('gender') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Diagn&oacute;stico Inicial</label>
                <textarea name="diagnosis" class="form-control-modern" rows="3">{{ old('diagnosis') }}</textarea>
            </div>

            @if(auth()->user()->admin)
            <div class="form-group">
                <label class="form-label">Fisioterapeuta Asignado *</label>
                <select name="user_id" class="form-control-modern" required>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" {{ (old('user_id') == $therapist->id || auth()->id() == $therapist->id) ? 'selected' : '' }}>
                            {{ $therapist->name }} ({{ $therapist->admin ? 'Admin' : 'Fisio' }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(auth()->user()->admin)
            <div class="form-group">
                <label class="form-label">Fecha de Ingreso (Opcional)</label>
                <input type="date" name="created_at" class="form-control-modern" value="{{ old('created_at') }}">
            </div>
            @endif

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary">Cancelar</a>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save"></i> Registrar Paciente
                </button>
            </div>
        </form>
    </div>

@endsection

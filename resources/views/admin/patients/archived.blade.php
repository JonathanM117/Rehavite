@extends('layouts.admin')

@section('title', 'Pacientes Archivados')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📦 Pacientes Archivados</h1>
        <p class="page-subtitle">Solicitudes pendientes y pacientes archivados — Solo visible para administradores</p>
    </div>
    <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Volver al índice
    </a>
</div>

@if(session('success'))
<div class="glass-card mb-24" style="border-left:4px solid var(--success); padding:14px 20px; color:var(--success);">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Pending Deletion --}}
@php $pending = $patients->where('status', 'pending_deletion'); $archived = $patients->where('status', 'archived'); @endphp

@if($pending->count())
<div class="glass-card mb-24">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid var(--border-color);">
        <span style="font-size:20px;">⏳</span>
        <h3 style="margin:0; font-size:16px;">Solicitudes Pendientes ({{ $pending->count() }})</h3>
        <span style="background:rgba(245,158,11,0.15); color:#f59e0b; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">Esperando decisión</span>
    </div>

    @foreach($pending as $patient)
    <div style="border:1px solid rgba(245,158,11,0.3); border-radius:10px; padding:16px; margin-bottom:12px; background:rgba(245,158,11,0.04);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
            <div>
                <h4 style="margin:0 0 4px; font-size:15px;">{{ $patient->full_name }}</h4>
                <div style="font-size:12px; color:var(--text-muted);">
                    <span>Exp: {{ $patient->expediente_id }}</span>
                    &nbsp;·&nbsp;
                    <span>Fisio: {{ $patient->user?->name ?? '—' }}</span>
                    &nbsp;·&nbsp;
                    <span>Solicitado por: <strong style="color:var(--text-secondary);">{{ $patient->deletionRequestedBy?->name ?? '—' }}</strong></span>
                    &nbsp;·&nbsp;
                    <span>{{ $patient->deletion_requested_at?->diffForHumans() ?? '—' }}</span>
                </div>
                @if($patient->diagnosis)
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">{{ Str::limit($patient->diagnosis, 80) }}</div>
                @endif
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" onsubmit="return confirm('¿Eliminar definitivamente a {{ $patient->full_name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-modern btn-sm" style="background:#ef4444; color:white;">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.patients.request-deletion', $patient) }}" onsubmit="return false;"
                      action="{{ route('admin.patients.deletion.reject', $patient) }}">
                    {{-- Quick reject from panel --}}
                </form>
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn-modern btn-secondary btn-sm">
                    <i class="fas fa-eye"></i> Ver expediente
                </a>
                <form method="GET" action="{{ route('admin.patients.deletion.reject', $patient) . '?signature=' . request()->query('signature') }}">
                </form>
                {{-- Admin direct actions --}}
                <form method="POST" action="{{ route('admin.patients.update', $patient) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $patient->name }}">
                    <input type="hidden" name="last_name" value="{{ $patient->last_name }}">
                    <input type="hidden" name="gender" value="{{ $patient->gender }}">
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn-modern btn-secondary btn-sm" style="border-color:rgba(16,185,129,0.4); color:#10b981;">
                        <i class="fas fa-undo"></i> Reactivar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.patients.update', $patient) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $patient->name }}">
                    <input type="hidden" name="last_name" value="{{ $patient->last_name }}">
                    <input type="hidden" name="gender" value="{{ $patient->gender }}">
                    <input type="hidden" name="status" value="archived">
                    <button type="submit" class="btn-modern btn-secondary btn-sm" style="border-color:rgba(59,130,246,0.4); color:#3b82f6;">
                        <i class="fas fa-archive"></i> Archivar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Archived --}}
@if($archived->count())
<div class="glass-card mb-24">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid var(--border-color);">
        <span style="font-size:20px;">📦</span>
        <h3 style="margin:0; font-size:16px;">Archivados ({{ $archived->count() }})</h3>
    </div>

    @foreach($archived as $patient)
    <div style="border:1px solid var(--border-color); border-radius:10px; padding:14px 16px; margin-bottom:10px; opacity:0.85;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:14px; font-weight:600;">{{ $patient->full_name }}</span>
                <span style="font-size:12px; color:var(--text-muted); margin-left:10px;">{{ $patient->expediente_id }}</span>
                <span style="font-size:12px; color:var(--text-muted); margin-left:10px;">Fisio: {{ $patient->user?->name ?? '—' }}</span>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn-modern btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
                <form method="POST" action="{{ route('admin.patients.update', $patient) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $patient->name }}">
                    <input type="hidden" name="last_name" value="{{ $patient->last_name }}">
                    <input type="hidden" name="gender" value="{{ $patient->gender }}">
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn-modern btn-secondary btn-sm" style="color:#10b981;">
                        <i class="fas fa-undo"></i> Restaurar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" onsubmit="return confirm('¿Eliminar definitivamente?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-modern btn-sm" style="background:#ef4444; color:white;">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($patients->isEmpty())
<div class="glass-card" style="text-align:center; padding:60px;">
    <i class="fas fa-inbox" style="font-size:48px; opacity:0.15; display:block; margin-bottom:16px;"></i>
    <p style="color:var(--text-secondary); font-size:18px;">No hay pacientes archivados ni solicitudes pendientes.</p>
</div>
@endif

@endsection

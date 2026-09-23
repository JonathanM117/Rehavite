@extends('layouts.admin')

@section('title', 'Papelera de Reciclaje')

@section('content')
@php $trashDays = (int) \App\Models\SystemSetting::get('trash_days', 15); @endphp
<div class="page-header">
    <div>
        <h1 class="page-title">🗑️ Papelera de Reciclaje</h1>
        <p class="page-subtitle">
            Pacientes eliminados. Se borrarán permanentemente después de
            <strong style="color:#f59e0b; font-size:16px; margin: 0 2px;">{{ $trashDays }}</strong>
            días.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al índice
        </a>
    </div>
</div>

@if(session('success'))
<div class="glass-card mb-24" style="border-left:4px solid var(--success); padding:14px 20px; color:var(--success);">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($patients->count())
<div class="glass-card mb-24">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid var(--border-color);">
        <span style="font-size:20px;">🗑️</span>
        <h3 style="margin:0; font-size:16px;">Pacientes en Papelera ({{ $patients->count() }})</h3>
    </div>

    @foreach($patients as $patient)
    <div style="border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:14px 16px; margin-bottom:10px; opacity:0.85; background:rgba(239,68,68,0.02);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:14px; font-weight:600;">{{ $patient->full_name }}</span>
                <span style="font-size:12px; color:var(--text-muted); margin-left:10px;">{{ $patient->expediente_id }}</span>
                <br>
                @php $daysLeft = max(0, $trashDays - $patient->deleted_at->diffInDays(now())); @endphp
                <span style="font-size:11px; color:var(--text-secondary);">
                    Eliminado {{ $patient->deleted_at->diffForHumans() }}
                    <span style="color:{{ $daysLeft <= 3 ? '#ef4444' : '#f59e0b' }}; margin-left:10px; font-weight:700;">
                        (Expira en <strong>{{ $daysLeft }}</strong> día{{ $daysLeft !== 1 ? 's' : '' }})
                    </span>
                </span>
            </div>
            <div style="display:flex; gap:8px;">
                <form method="POST" action="{{ route('admin.patients.restore', $patient->id) }}">
                    @csrf
                    <button type="submit" class="btn-modern btn-secondary btn-sm" style="color:#10b981; border-color:rgba(16,185,129,0.4);">
                        <i class="fas fa-trash-restore"></i> Restaurar
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.patients.force-delete', $patient->id) }}" onsubmit="return confirm('¿Estás SEGURO de eliminar definitivamente a este paciente? Esta acción NO se puede deshacer y perderás toda la información.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-modern btn-sm" style="background:#ef4444; color:white;">
                        <i class="fas fa-ban"></i> Borrar para siempre
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top: 20px;">
    {{ $patients->links() }}
</div>

@else
<div class="glass-card" style="text-align:center; padding:60px;">
    <i class="fas fa-trash-alt" style="font-size:48px; opacity:0.15; display:block; margin-bottom:16px;"></i>
    <p style="color:var(--text-secondary); font-size:18px;">La papelera está vacía.</p>
</div>
@endif

@endsection

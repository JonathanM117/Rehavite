<div>
    {{-- Period Selector --}}
    <div class="flex-between mb-24">
        <div style="display:flex; gap:8px;">
            <button wire:click="$set('period', 'week')" class="btn-modern btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-secondary' }}">
                Esta Semana
            </button>
            <button wire:click="$set('period', 'month')" class="btn-modern btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-secondary' }}">
                Este Mes
            </button>
            <button wire:click="$set('period', 'all')" class="btn-modern btn-sm {{ $period === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                Todo el tiempo
            </button>
        </div>
        <span style="font-size:13px; color:var(--text-muted);">
            <i class="fas fa-sync" wire:loading></i>
            Actualizado en tiempo real
        </span>
    </div>

    {{-- Global Stats Row --}}
    <div class="grid grid-4 gap-16 mb-24" style="display:grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom:24px;">
        <div class="glass-card" style="padding:20px; text-align:center; border-top: 3px solid var(--accent-primary);">
            <div style="font-size:32px; font-weight:700; color:var(--accent-primary);">{{ $stats['total_patients'] }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px; text-transform:uppercase; letter-spacing:0.5px;">Pacientes Totales</div>
        </div>
        <div class="glass-card" style="padding:20px; text-align:center; border-top: 3px solid #6c63ff;">
            <div style="font-size:32px; font-weight:700; color:#6c63ff;">{{ $stats['total_consultations'] }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px; text-transform:uppercase; letter-spacing:0.5px;">Consultas del Período</div>
        </div>
        <div class="glass-card" style="padding:20px; text-align:center; border-top: 3px solid #22c55e;">
            <div style="font-size:32px; font-weight:700; color:#22c55e;">${{ number_format($stats['total_payments'], 2) }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px; text-transform:uppercase; letter-spacing:0.5px;">Facturado del Período</div>
        </div>
        <div class="glass-card" style="padding:20px; text-align:center; border-top: 3px solid #f59e0b;">
            <div style="font-size:32px; font-weight:700; color:#f59e0b;">{{ $stats['active_therapists'] }}</div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px; text-transform:uppercase; letter-spacing:0.5px;">Fisioterapeutas</div>
        </div>
    </div>

    {{-- Therapist Cards --}}
    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($therapists as $therapist)
        <div class="glass-card" style="padding:24px; display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
            {{-- Avatar --}}
            <img src="{{ $therapist->avatar_url }}" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:3px solid {{ $therapist->admin ? 'var(--accent-primary)' : 'rgba(255,255,255,0.1)' }}; flex-shrink:0;">

            {{-- Name & Role --}}
            <div style="flex:0 0 200px;">
                <div style="font-weight:700; font-size:16px;">{{ $therapist->name }}</div>
                <div class="badge-modern {{ $therapist->admin ? 'badge-info' : 'badge-active' }}" style="font-size:10px; display:inline-block; margin-top:4px;">
                    {{ $therapist->admin ? 'Administrador' : 'Fisioterapeuta' }}
                </div>
            </div>

            {{-- Stats --}}
            <div style="display:flex; gap:32px; flex:1; flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:24px; font-weight:700; color:var(--accent-primary);">{{ $therapist->total_patients }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">Pacientes</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:24px; font-weight:700; color:#6c63ff;">{{ $therapist->period_consultations }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">Consultas</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:24px; font-weight:700; color:#22c55e;">${{ number_format($therapist->total_billed, 2) }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">Facturado</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:14px; font-weight:600; color:var(--text-secondary);">
                        {{ $therapist->last_activity ? $therapist->last_activity->diffForHumans() : 'Sin actividad' }}
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">Última Consulta</div>
                </div>
            </div>

            {{-- Action: View patients --}}
            @if(auth()->user()->admin)
            <a href="{{ route('admin.patients.index', ['search' => $therapist->name]) }}" class="btn-modern btn-secondary btn-sm" style="flex-shrink:0;">
                <i class="fas fa-users"></i> Ver Pacientes
            </a>
            @endif
        </div>
        @endforeach

        @if($therapists->isEmpty())
        <div class="empty-state">
            <i class="fas fa-user-md"></i>
            <p>No hay fisioterapeutas registrados aún.</p>
        </div>
        @endif
    </div>
</div>

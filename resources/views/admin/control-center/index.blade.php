@extends('layouts.admin')

@section('title', 'Centro de Control')
@section('page-title', 'Centro de Control')

@section('content')

<div x-data="{ tab: 'reports' }">
    {{-- Tab Navigation --}}
    <div style="display:flex; gap:8px; margin-bottom:24px; border-bottom: 1px solid rgba(255,255,255,0.07); padding-bottom:16px;">
        <button @click="tab = 'reports'"
            :class="tab === 'reports' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-chart-bar"></i> Actividad del Equipo
        </button>
        <button @click="tab = 'editor'"
            :class="tab === 'editor' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-paint-brush"></i> Editor de Landing Page
        </button>
    </div>

    {{-- Activity Report Tab --}}
    <div x-show="tab === 'reports'" x-cloak>
        <div class="mb-16">
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:700;">Reporte de Actividad</h2>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">Monitorea el desempeño de cada fisioterapeuta en la clínica.</p>
        </div>
        <livewire:activity-report />
    </div>

    {{-- Landing Page Editor Tab --}}
    <div x-show="tab === 'editor'" x-cloak>
        <div class="mb-16">
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:700;">Editor de Landing Page</h2>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">
                Modifica el contenido de tu sitio público sin tocar código.
                <a href="{{ url('/') }}" target="_blank" style="color:var(--accent-primary); margin-left:8px;">
                    <i class="fas fa-external-link-alt"></i> Ver sitio público
                </a>
            </p>
        </div>
        <livewire:landing-editor />
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection

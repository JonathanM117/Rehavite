@extends('layouts.admin')

@section('title', 'Centro de Control')
@section('page-title', 'Centro de Control')

@section('content')

<div x-data="{ tab: 'reports' }">
    {{-- Tab Navigation --}}
    <div style="display:flex; gap:8px; margin-bottom:24px; border-bottom: 1px solid rgba(255,255,255,0.07); padding-bottom:16px; flex-wrap:wrap;">
        <button @click="tab = 'reports'"
            :class="tab === 'reports' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-chart-bar"></i> Actividad del Equipo
        </button>
        <button @click="tab = 'history'"
            :class="tab === 'history' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-history"></i> Historial Global
        </button>
        <button @click="tab = 'settings'"
            :class="tab === 'settings' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-cog"></i> Configuraciones
        </button>
        <button @click="tab = 'backup'"
            :class="tab === 'backup' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-database"></i> Respaldos
        </button>
        <button @click="tab = 'exercise-bank'"
            :class="tab === 'exercise-bank' ? 'btn-primary' : 'btn-secondary'"
            class="btn-modern btn-sm">
            <i class="fas fa-dumbbell"></i> Banco de Ejercicios
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

    {{-- Global History Tab --}}
    <div x-show="tab === 'history'" x-cloak>
        <div class="mb-16">
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:700;">Historial Global</h2>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">
                Registro completo de todas las acciones del sistema. Las acciones repetidas en un mismo período de tiempo se agrupan.
            </p>
        </div>
        <div class="glass-card" style="max-height:70vh; overflow-y:auto;">
            <livewire:global-history />
        </div>
    </div>

    {{-- Site Settings Tab --}}
    <div x-show="tab === 'settings'" x-cloak>
        <div class="mb-16">
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:700;">Configuraciones del Sistema</h2>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">
                Ajusta el comportamiento del sistema según las necesidades de la clínica.
            </p>
        </div>
        <livewire:site-settings />
    </div>

    {{-- Backup Manager Tab --}}
    <div x-show="tab === 'backup'" x-cloak>
        <div class="mb-16">
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:700;">Respaldos y Recuperación</h2>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">
                Administra las copias de seguridad de la base de datos de la plataforma.
              </p>
          </div>
          <livewire:backup-manager />
      </div>

    {{-- Exercise Bank Tab --}}
    <div x-show="tab === 'exercise-bank'" x-cloak>
        <livewire:exercise-bank-manager />
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection

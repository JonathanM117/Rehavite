<div x-data="{}">

    {{-- ═══ PATIENT HEADER ═══ --}}
    <div class="glass-card mb-24" wire:key="patient-header" style="position: relative; z-index: 20;">
        <div class="flex-between">
            <div class="flex-center">
                <div
                    style="width:56px;height:56px;border-radius:50%;background:var(--accent-gradient);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:white;">
                    {{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->name), 0, 1)) }}{{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->last_name), 0, 1)) }}
                </div>
                <div>
                    <h2 style="margin:0;font-size:22px;font-weight:700;">{{ $patient->full_name }}</h2>
                    <div style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
                        <span style="color:var(--text-muted);">Expediente:</span> {{ $patient->expediente_id }}
                        @if($patient->birth_date)
                            &nbsp;·&nbsp;{{ $patient->age_label }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex-center gap-8">
                @if(auth()->user()->admin || $patient->user_id === auth()->id())
                    <button class="btn-modern btn-secondary btn-sm" wire:click="openCollaboratorModal">
                        <i class="fas fa-user-friends"></i> Colaboradores
                    </button>
                @endif
                {{-- Exportar (PDF / Excel) — Admin only --}}
                @if(auth()->user()->admin)
                <div x-data="{ openExport: false }" style="position:relative;">
                    <button type="button" class="btn-modern btn-sm" @click="openExport = !openExport" @click.outside="openExport = false"
                            style="background: linear-gradient(135deg,#0E6E6E,#0a5252); color:white; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-download"></i> Exportar
                        <i class="fas fa-caret-down" style="font-size:10px;"></i>
                    </button>
                    <div x-show="openExport" x-cloak x-transition
                         style="position:absolute; right:0; top:calc(100% + 6px); z-index:50; min-width:180px; background:var(--bg-app); border:1px solid rgba(255,255,255,0.1); border-radius:10px; box-shadow:0 12px 32px rgba(0,0,0,0.4); padding:8px; display:flex; flex-direction:column; gap:4px;">
                        <a href="{{ route('admin.reports.patient.pdf', $patient) }}" target="_blank"
                           style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; text-decoration:none; color:var(--text-primary); font-size:13px;"
                           onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-file-pdf" style="color:#e74c3c;"></i> Descargar PDF
                        </a>
                        <a href="{{ route('admin.reports.patient.excel', $patient) }}"
                           style="display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; text-decoration:none; color:var(--text-primary); font-size:13px;"
                           onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-file-excel" style="color:#27ae60;"></i> Descargar Excel
                        </a>
                    </div>
                </div>
                @endif
                <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>

                {{-- ═══ MENU DE OPCIONES (tres líneas) ═══ --}}
                @php $isAdminHeader = auth()->user()->admin; @endphp
                <div x-data="{ openMenu: false }" style="position:relative;">
                    <button type="button" class="btn-icon" @click="openMenu = !openMenu" @click.outside="openMenu = false" title="Más opciones">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div x-show="openMenu" x-cloak x-transition
                         style="position:absolute; right:0; top:calc(100% + 6px); z-index:50; min-width:240px; background:var(--bg-app); border:1px solid rgba(255,255,255,0.1); border-radius:10px; box-shadow:0 12px 32px rgba(0,0,0,0.4); padding:8px; display:flex; flex-direction:column; gap:4px;">
                        @if($isAdminHeader)
                            @if($patient->isPendingDeletion())
                                <div style="font-size:12px; color:#f59e0b; padding:8px 10px; background:rgba(245,158,11,0.08); border-radius:6px; margin-bottom:4px;">
                                    <i class="fas fa-exclamation-triangle"></i> Solicitud de eliminación pendiente
                                    @if($patient->deletionRequestedBy)
                                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Por {{ $patient->deletionRequestedBy->name }} · {{ $patient->deletion_requested_at?->diffForHumans() }}</div>
                                    @endif
                                </div>
                                <form action="{{ route('admin.patients.deletion.confirm', $patient) }}" method="POST" onsubmit="return confirm('¿Confirmar la eliminación definitiva de este paciente?')">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.2);"><i class="fas fa-trash"></i> Confirmar eliminación</button>
                                </form>
                                <form action="{{ route('admin.patients.deletion.reject', $patient) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2);"><i class="fas fa-undo"></i> Rechazar — Mantener activo</button>
                                </form>
                                <form action="{{ route('admin.patients.deletion.archive', $patient) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2);"><i class="fas fa-archive"></i> Archivar paciente</button>
                                </form>
                            @else
                                @if(!$patient->isArchived())
                                <form action="{{ route('admin.patients.deletion.archive', $patient) }}" method="POST" onsubmit="return confirm('¿Archivar a este paciente?')">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2);"><i class="fas fa-archive"></i> Archivar paciente</button>
                                </form>
                                @endif
                                <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('¿Eliminar permanentemente a este paciente? Esta acción es irreversible.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.2);"><i class="fas fa-trash"></i> Eliminar paciente</button>
                                </form>
                            @endif
                        @elseif($patient->user_id === auth()->id())
                            @if($patient->isPendingDeletion())
                                <div style="font-size:12px; color:#f59e0b; padding:8px 10px;"><i class="fas fa-clock"></i> Solicitud de eliminación pendiente</div>
                            @else
                                <form action="{{ route('admin.patients.request-deletion', $patient) }}" method="POST" onsubmit="return confirm('¿Solicitar la eliminación de este paciente? La administradora deberá confirmarlo.')">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-sm" style="width:100%; justify-content:flex-start; background:rgba(245,158,11,0.1); color:#f59e0b; border:1px solid rgba(245,158,11,0.2);"><i class="fas fa-flag"></i> Solicitar eliminación</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Contextual collaborator/owner info --}}
        @php
            $isOwner = $patient->user_id === auth()->id();
            $isAdmin = auth()->user()->admin;
            $isCollaborator = !$isOwner && !$isAdmin;
        @endphp
        <div
            style="margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            {{-- Show therapist owner --}}
            <span style="font-size:12px; color:var(--text-muted);">
                <i class="fas fa-user-md"></i> Fisio a cargo:
                <strong style="color:var(--text-primary);">{{ $patient->user->name ?? '—' }}</strong>
            </span>

            @if($patient->collaborators->count() > 0)
                <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">
                    <i class="fas fa-user-friends"></i> Colaboradores:
                </span>
                @foreach($patient->collaborators as $collab)
                    <span class="badge-modern badge-info" style="font-size:11px; padding:3px 8px;">{{ $collab->name }}</span>
                @endforeach
            @endif

            @if($isCollaborator)
                <span
                    style="font-size:11px; padding:3px 10px; background:rgba(99,102,241,0.15); color:#818cf8; border-radius:6px; margin-left:auto;">
                    <i class="fas fa-eye"></i> Acceso de colaborador
                </span>
            @endif
        </div>
    </div>

    {{-- ═══ TABS ═══ --}}
    <div class="tabs-modern" wire:key="navigation-tabs-bar">
        <button class="tab-item {{ $activeTab === 'expediente' ? 'active' : '' }}"
            @click="$wire.setTab('expediente')">
            <i class="fas fa-file-medical"></i> Expediente
        </button>
        <button class="tab-item {{ $activeTab === 'consultas' ? 'active' : '' }}"
            @click="$wire.setTab('consultas')">
            <i class="fas fa-stethoscope"></i> Episodio Clínico ({{ $patient->consultations->count() }})
        </button>
        <button class="tab-item {{ $activeTab === 'documentos' ? 'active' : '' }}"
            @click="$wire.setTab('documentos')">
            <i class="fas fa-folder-open"></i> Documentos Clínicos ({{ $patient->clinicalDocuments->count() }})
        </button>
        @if(auth()->user()->admin)
        <button class="tab-item {{ $activeTab === 'historial' ? 'active' : '' }}"
            @click="$wire.setTab('historial')">
            <i class="fas fa-history"></i> Historial
        </button>
        @endif
        <a href="{{ route('admin.patients.exercise-plan', $patient) }}" target="_blank"
            class="tab-item" style="margin-left:auto; text-decoration:none; background:rgba(14,110,110,0.12); color:#0E6E6E; border:1px solid rgba(14,110,110,0.3);">
            <i class="fas fa-dumbbell"></i> Plan de ejercicios
            <i class="fas fa-external-link-alt" style="font-size:10px; margin-left:4px;"></i>
        </a>
    </div>


    {{-- ═══ TAB: EXPEDIENTE ═══ --}}
    {{-- When form is dirty: always visible and elevated above the overlay via z-9500 --}}
    @php $expedienteDisplay = $activeTab === 'expediente' ? 'block' : 'none'; @endphp
    <div wire:key="tab-wrapper-expediente"
         id="expediente-tab-content"
         style="display:{{ $expedienteDisplay }};">
        {{-- Static View --}}
        @include('livewire.partials.medical-record-static')

            {{-- Acciones de Administración (archivar/eliminar) ahora viven en el menú de tres líneas del header --}}

            {{-- Modal de Edición del Expediente --}}
            <div id="medical-record-modal"
                 onclick="if(event.target===this) handleUnsavedExit('medical-record-form', () => { document.getElementById('medical-record-form').reset(); document.getElementById('medical-record-modal').style.display='none'; document.body.style.overflow=''; })"
                 style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); padding:24px; overflow-y:auto; align-items:flex-start; justify-content:center;">
                <div style="background:var(--bg-app); max-width:1100px; width:100%; margin:40px auto; border-radius:16px; padding:32px; position:relative; box-shadow:0 30px 80px rgba(0,0,0,0.6);">
                    
                    <div class="flex-between" style="margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:16px;">
                        <h2 style="margin:0; font-size:20px; font-weight:700;"><i class="fas fa-pen"></i> Editar Expediente Médico</h2>
                        <button type="button" 
                                onclick="handleUnsavedExit('medical-record-form', () => { document.getElementById('medical-record-form').reset(); document.getElementById('medical-record-modal').style.display='none'; document.body.style.overflow=''; })"
                                style="background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); cursor:pointer;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

            <form id="medical-record-form"
                  action="{{ route('admin.patients.update', $patient) }}" method="POST"
                  oninput="this.dataset.dirty = 'true'"
                  onchange="this.dataset.dirty = 'true'"
                  onsubmit="delete this.dataset.dirty">
                @csrf @method('PUT')

                {{-- Controles de Guardado (fijos en la parte inferior o superior) --}}
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom: 24px;">
                    <button type="button"
                            onclick="handleUnsavedExit('medical-record-form', () => { document.getElementById('medical-record-form').reset(); document.getElementById('medical-record-modal').style.display='none'; document.body.style.overflow=''; })"
                            class="btn-modern btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

                @include("livewire.partials.expediente-form-fields", ["patient" => $patient])

                {{-- ═══ SAVE BUTTON ═══ --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:32px;">
                    <button type="submit" id="medical-record-submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Historia Clínica
                    </button>
                </div>
            </form>
            </div>
            </div>
    </div>

    {{-- ═══ TAB: CONSULTAS ═══ --}}
    <div wire:key="tab-wrapper-consultas" style="display: {{ $activeTab === 'consultas' ? 'block' : 'none' }};">
        <style>
            .md-layout { display: flex; gap: 24px; align-items: flex-start; }
            .md-list { flex: 0 0 35%; max-width: 380px; display: flex; flex-direction: column; gap: 12px; }
            .md-detail { flex: 1; min-width: 0; }
            
            @media (max-width: 900px) {
                .md-layout { display: block; }
                .md-list.is-active { display: none; }
                .md-detail.is-hidden { display: none; }
                .md-list { max-width: none; }
            }
        </style>

        <div class="md-layout" wire:key="consultations-master-detail">
            {{-- Master List --}}
            <div class="md-list {{ $selectedConsultationId ? 'is-active' : '' }}">
                <div class="flex-between mb-16" wire:key="consultas-header">
                    <div style="font-size:14px;color:var(--text-secondary);">
                        {{ $patient->consultations->count() }} episodio(s)
                    </div>
                    <button class="btn-modern btn-primary btn-sm" wire:click="createConsultation"
                            wire:loading.attr="disabled" wire:target="createConsultation">
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="createConsultation"></i>
                        <i class="fas fa-plus" wire:loading.remove wire:target="createConsultation"></i>
                        Nuevo Episodio
                    </button>
                </div>

                <div style="display:flex; flex-direction:column; gap:16px;">
                    @php
                        $consultasByYear = $patient->consultations
                            ->sortByDesc('created_at')
                            ->groupBy(fn($c) => $c->created_at->format('Y'));
                    @endphp
                    @forelse ($consultasByYear as $year => $consultasDelAnio)
                        <div wire:key="year-group-{{ $year }}">
                            <div style="font-size:13px; font-weight:700; color:var(--accent-primary); margin-bottom:8px;">{{ $year }}</div>
                            <div style="display:flex; flex-direction:column; gap:12px;">
                                @foreach ($consultasDelAnio as $consulta)
                                    @php
                                        $numSesiones = $consulta->evolutionNotes->count();
                                        $ultimaSesion = $consulta->evolutionNotes->first();
                                    @endphp
                                    <div class="consultation-card"
                                         wire:click="selectConsultation({{ $consulta->id }})"
                                         style="cursor:pointer; transition:all 0.2s; {{ $selectedConsultationId == $consulta->id ? 'border-color:var(--accent-primary); background:rgba(var(--accent-primary-rgb),0.05); transform:translateX(4px); box-shadow: -4px 0 0 var(--accent-primary);' : '' }}"
                                         wire:key="consultation-{{ $consulta->id }}">
                                        <div class="flex-between mb-8">
                                            <span class="badge-modern badge-{{ $consulta->status }}">
                                                {{ $consulta->status_label }}
                                            </span>
                                            <span style="font-size:12px;color:var(--text-muted);">
                                                {{ $consulta->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        @if($consulta->diagnosis)
                                            <div style="font-weight:600;margin-bottom:4px; font-size:14px;">{{ Str::limit($consulta->diagnosis, 50) }}</div>
                                            <div style="font-size:13px;color:var(--text-secondary);">{{ Str::limit($consulta->reason, 60) }}</div>
                                        @else
                                            <div style="font-weight:600;margin-bottom:4px; font-size:14px;">{{ Str::limit($consulta->reason, 50) }}</div>
                                        @endif
                                        <div style="display:flex; align-items:center; justify-content:center; gap:16px; margin-top:12px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06); font-size:12px; color:var(--text-secondary);">
                                            <div style="text-align:center;">
                                                <div style="font-weight:700; font-size:14px; color:var(--text-primary);">{{ $numSesiones }}</div>
                                                <div>sesion{{ $numSesiones === 1 ? '' : 'es' }}</div>
                                            </div>
                                            <div style="width:1px; height:24px; background:rgba(255,255,255,0.1);"></div>
                                            <div style="text-align:center;">
                                                <div style="font-weight:700; font-size:14px; color:var(--text-primary);">{{ $ultimaSesion?->date?->format('d/m/Y') ?? '—' }}</div>
                                                <div>última sesión</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:32px 16px;">
                            <i class="fas fa-stethoscope" style="font-size:32px;"></i>
                            <p style="font-size:14px;">No hay episodios registrados</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Detail View --}}
            @php
                $activeConsultation = $selectedConsultationId 
                    ? $patient->consultations->where('id', $selectedConsultationId)->first() 
                    : $patient->consultations->first();
            @endphp

            @if($activeConsultation)
                <div class="md-detail" style="display: {{ $selectedConsultationId ? 'block' : 'none' }}; width: 100%;" wire:key="consultation-detail-container-{{ $activeConsultation->id }}">
                    <livewire:consultation-detail 
                        :consultation="$activeConsultation" 
                        :isNew="$newlyCreatedConsultationId == $activeConsultation->id" 
                        :wire:key="'detail-'.$activeConsultation->id" />
                </div>
            @endif

            <div class="md-detail {{ $activeConsultation ? 'is-hidden' : '' }}" style="display: {{ !$selectedConsultationId ? 'flex' : 'none' }}; flex-direction:column; justify-content:center; align-items:center; width: 100%;" wire:key="consultation-detail-empty">
                <div class="empty-state glass-card" style="height: 100%; min-height: 400px; display:flex; flex-direction:column; justify-content:center; align-items:center; width: 100%;">
                    <i class="fas fa-hand-pointer" style="font-size:48px; color:var(--text-muted); margin-bottom:16px;"></i>
                    <p style="color:var(--text-secondary); font-size:16px;">Selecciona una consulta de la lista para ver sus detalles</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ TAB: DOCUMENTOS CLINICOS ═══ --}}
    <div wire:key="tab-wrapper-documentos" style="display: {{ $activeTab === 'documentos' ? 'block' : 'none' }};">
        <livewire:clinical-documents :patientId="$patient->id" wire:key="clinical-documents-{{ $patient->id }}" />
    </div>

    {{-- ═══ TAB: HISTORIAL (solo administrador) ═══ --}}
    @if(auth()->user()->admin)
    <div wire:key="tab-wrapper-historial" style="display: {{ $activeTab === 'historial' ? 'block' : 'none' }};">
        <div class="glass-card">
            <div class="section-header">
                <i class="fas fa-history"></i>
                <h3>Historial de Cambios</h3>
            </div>
            @php $logs = $patient->auditLogs()->with('user')->take(50)->get(); @endphp
            @if($logs->count() > 0)
                <div style="max-height:500px; overflow-y:auto;">
                    @foreach($logs as $log)
                        <div style="display:flex; align-items:flex-start; gap:12px; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.05);"
                            wire:key="audit-{{ $log->id }}">
                            <div
                                style="width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fas fa-{{ $log->action === 'created' ? 'plus' : ($log->action === 'deleted' ? 'trash' : 'edit') }}"
                                    style="font-size:12px; color:{{ $log->action === 'created' ? 'var(--success)' : ($log->action === 'deleted' ? 'var(--danger)' : 'var(--accent-primary)') }};"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:13px; color:var(--text-primary);">
                                    <strong>{{ $log->user->name ?? 'Sistema' }}</strong>
                                    {{ $log->description ?? $log->action }}
                                </div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                    {{ $log->created_at->diffForHumans() }} · {{ $log->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No hay registros de actividad aún</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══ COLLABORATOR MODAL ═══ --}}
    @if($showCollaboratorModal)
        <div class="slide-panel-overlay" style="display:block; z-index:1000;"
            wire:click="$set('showCollaboratorModal', false)"></div>
        <div class="glass-card"
            style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1001; max-width:500px; width:90%; padding:32px;">
            <div class="flex-between mb-24">
                <h3 style="margin:0;"><i class="fas fa-user-friends" style="color:var(--accent-primary);"></i> Gestionar
                    Colaboradores</h3>
                <button class="btn-icon" wire:click="$set('showCollaboratorModal', false)"><i
                        class="fas fa-times"></i></button>
            </div>

            {{-- Current collaborators --}}
            @if($patient->collaborators->count() > 0)
                <div style="margin-bottom:20px;">
                    <div
                        style="font-size:12px; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">
                        Colaboradores actuales</div>
                    @foreach($patient->collaborators as $collab)
                        <div
                            style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:rgba(255,255,255,0.03); border-radius:8px; margin-bottom:6px;">
                            <div>
                                <span style="font-weight:600;">{{ $collab->name }}</span>
                                <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">{{ $collab->email }}</span>
                                <span style="font-size:10px; color:var(--text-muted); display:block;">Asignado por: {{ \App\Models\User::find($collab->pivot->granted_by)->name ?? 'Admin' }}</span>
                            </div>
                            <button wire:click="removeCollaborator({{ $collab->id }})" class="btn-modern btn-sm"
                                style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); padding:4px 8px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add new collaborator --}}
            <div
                style="font-size:12px; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">
                Agregar colaborador</div>
            <div style="display:flex; gap:8px;">
                <select wire:model.live="selectedCollaboratorId" class="form-control-modern" style="flex:1;">
                    <option value="">Seleccionar fisioterapeuta...</option>
                    @foreach($this->availableCollaborators as $therapist)
                        <option value="{{ $therapist->id }}">{{ $therapist->name }} ({{ $therapist->email }})</option>
                    @endforeach
                </select>
                <button wire:click="addCollaborator" class="btn-modern btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
            <p style="font-size:11px; color:var(--text-muted); margin-top:8px;">
                Los colaboradores podrán ver los datos del paciente y crear notas de evolución, pero no podrán eliminar al
                paciente ni modificar su expediente médico.
            </p>
        </div>
    @endif

    <style>
        .consultation-section {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 16px;
        }

        .consultation-section>summary {
            padding: 4px 0;
        }

        .consultation-section>summary h3 {
            font-size: 15px;
            margin: 0 0 0 8px;
        }

        .consultation-section[open]>summary .fa-chevron-down {
            transform: rotate(180deg);
        }

        .consultation-section>summary::-webkit-details-marker {
            display: none;
        }
    </style>
</div>

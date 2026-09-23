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
                <a href="{{ route('admin.patients.edit', $patient) }}" class="btn-modern btn-secondary btn-sm">
                        <i class="fas fa-pen"></i> Editar
                    </a>
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
        @php $record = $patient->medicalRecord; @endphp

        @if(!$record)
            <div class="glass-card">
                <div class="empty-state">
                    <i class="fas fa-file-medical"></i>
                    <p>No hay historia clínica registrada para este paciente</p>
                    <form action="{{ route('admin.medical-records.store') }}" method="POST" style="margin-top:16px;">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <input type="hidden" name="age" value="{{ $patient->age }}">
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="fas fa-plus"></i> Crear Historia Clínica
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- Static View --}}
            @include('livewire.partials.medical-record-static')

            {{-- Acciones de Administración (archivar/eliminar) ahora viven en el menú de tres líneas del header --}}

            {{-- Modal de Edición del Expediente --}}
            <div id="medical-record-modal"
                 onclick="if(event.target===this) {
                     const form = document.getElementById('medical-record-form');
                     if (form && form.dataset.dirty === 'true') {
                         if (confirm('Tienes cambios sin guardar, ¿deseas guardar?')) {
                             form.submit();
                         }
                     } else {
                         if (form) { form.reset(); delete form.dataset.dirty; }
                         this.style.display = 'none';
                         document.body.style.overflow = '';
                     }
                 }"
                 style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); padding:24px; overflow-y:auto; align-items:flex-start; justify-content:center;">
                <div style="background:var(--bg-app); max-width:1100px; width:100%; margin:40px auto; border-radius:16px; padding:32px; position:relative; box-shadow:0 30px 80px rgba(0,0,0,0.6);">
                    
                    <div class="flex-between" style="margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:16px;">
                        <h2 style="margin:0; font-size:20px; font-weight:700;"><i class="fas fa-pen"></i> Editar Expediente Médico</h2>
                        <button type="button" 
                                onclick="const form = document.getElementById('medical-record-form'); if(form && form.dataset.dirty === 'true' && !confirm('Tienes cambios sin guardar en el expediente. ¿Seguro que deseas salir sin guardar?')) return; if(form) { form.reset(); delete form.dataset.dirty; } document.getElementById('medical-record-modal').style.display='none'; document.body.style.overflow='';"
                                style="background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); cursor:pointer;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

            <form id="medical-record-form"
                  action="{{ route('admin.medical-records.update', $record) }}" method="POST"
                  oninput="this.dataset.dirty = 'true'"
                  onchange="this.dataset.dirty = 'true'"
                  onsubmit="delete this.dataset.dirty">
                @csrf @method('PUT')

                {{-- Controles de Guardado (fijos en la parte inferior o superior) --}}
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom: 24px;">
                    <button type="button"
                            onclick="const form = document.getElementById('medical-record-form'); if(form && form.dataset.dirty === 'true' && !confirm('Tienes cambios sin guardar en el expediente. ¿Seguro que deseas salir sin guardar?')) return; if(form) { form.reset(); delete form.dataset.dirty; } document.getElementById('medical-record-modal').style.display='none'; document.body.style.overflow='';"
                            class="btn-modern btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

                {{-- ═══ DATOS PERSONALES (Sección 0) ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-user"></i>
                        <h3>Datos Personales</h3>
                    </div>
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Edad</label>
                            @php
                                $autoAge = old('age', $record->age ?? ($patient->birth_date ? $patient->birth_date->age : null));
                            @endphp
                            <input type="number" name="age" class="form-control-modern"
                                value="{{ $autoAge }}" min="0" max="130">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado Civil</label>
                            <input type="text" name="marital_status" class="form-control-modern"
                                value="{{ old('marital_status', $record->marital_status) }}" placeholder="Soltero, Casado, Viudo">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Domicilio</label>
                            <input type="text" name="address" class="form-control-modern"
                                value="{{ old('address', $record->address) }}" placeholder="Colonia, Fraccionamiento">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lateralidad</label>
                            <input type="text" name="laterality" class="form-control-modern"
                                value="{{ old('laterality', $record->laterality) }}"
                                placeholder="Diestro, Zurdo, Ambidiestro">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Escolaridad / Ocupación</label>
                            <input type="text" name="occupation" class="form-control-modern"
                                value="{{ old('occupation', $record->occupation) }}" placeholder="Nivel de estudios, Ocupación">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Dependencia Económica</label>
                            <input type="text" name="economic_dependency" class="form-control-modern"
                                value="{{ old('economic_dependency', $record->economic_dependency) }}" placeholder="Independiente, Dependiente de padres, Pareja, etc.">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Cuidador Primario</label>
                            <input type="text" name="primary_caregiver" class="form-control-modern"
                                value="{{ old('primary_caregiver', $record->primary_caregiver) }}" placeholder="(En caso de incapacidad del paciente)">
                        </div>
                    </div>
                </div>

                {{-- ═══ FUENTE DE REFERENCIA ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-bullhorn"></i>
                        <h3>Fuente de Referencia</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">&iquest;C&oacute;mo se enter&oacute; de nuestros servicios?</label>
                            <input type="text" name="personal_non_pathological[referencia_como_entero]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.referencia_como_entero', $record->personal_non_pathological['referencia_como_entero'] ?? '') }}" placeholder="Redes sociales, Recomendación, Ubicación, Google">
                        </div>
                        <div class="form-group">
                            <label class="form-label">M&eacute;dico remitente</label>
                            <input type="text" name="personal_non_pathological[referencia_medico]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.referencia_medico', $record->personal_non_pathological['referencia_medico'] ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ CONTACTO DE EMERGENCIA ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-phone-alt"></i>
                        <h3>Contacto de Emergencia</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="personal_non_pathological[emergencia_nombre]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_nombre', $record->personal_non_pathological['emergencia_nombre'] ?? '') }}" placeholder="(En caso de emergencia en consultorio)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tel&eacute;fono</label>
                            <input type="text" name="personal_non_pathological[emergencia_telefono]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_telefono', $record->personal_non_pathological['emergencia_telefono'] ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Parentesco</label>
                            <input type="text" name="personal_non_pathological[emergencia_parentesco]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_parentesco', $record->personal_non_pathological['emergencia_parentesco'] ?? '') }}" placeholder="Madre, Padre, Hermano, Esposo">
                        </div>
                    </div>
                </div>

                {{-- ═══ REPRESENTANTE LEGAL (Sección 1) ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-users"></i>
                        <h3>Representante Legal</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="guardian_name" class="form-control-modern"
                                value="{{ old('guardian_name', $record->guardian_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="guardian_birth_date" class="form-control-modern"
                                value="{{ old('guardian_birth_date', $record->guardian_birth_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Edad</label>
                            <input type="number" name="guardian_age" class="form-control-modern"
                                value="{{ old('guardian_age', $record->guardian_age) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sexo</label>
                            <input type="text" name="guardian_gender" class="form-control-modern"
                                value="{{ old('guardian_gender', $record->guardian_gender) }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ SECCIÓN PEDIÁTRICA ═══ --}}
                @php
                    $ped = $record->pediatric_history ?? [];
                    $hasPediatricData = !empty($ped['is_pediatric']) || 
                                        !empty($ped['gestational_age']) || 
                                        !empty($ped['milestone_head_control']) || 
                                        !empty($ped['milestone_sitting']) || 
                                        !empty($ped['milestone_walking']);
                @endphp
                <div class="glass-card mb-24" x-data="{ isPediatric: {{ $hasPediatricData ? 'true' : 'false' }} }">
                    <div class="section-header">
                        <i class="fas fa-baby"></i>
                        <h3>Sección Pediátrica (Paciente Infantil)</h3>
                        <label style="margin-left:auto; display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px; font-weight:600; color:var(--accent-primary);">
                            <input type="checkbox" name="pediatric_history[is_pediatric]" value="1" x-model="isPediatric" style="cursor:pointer;" {{ $hasPediatricData ? 'checked' : '' }}>
                            Es Paciente Pediátrico
                        </label>
                    </div>
                    
                    <div x-show="isPediatric" x-cloak style="margin-top:16px;">
                        <h4 style="margin:0 0 12px; font-size:14px; font-weight:600; color:var(--text-secondary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">Antecedentes Perinatales</h4>
                        <div class="grid grid-3 mb-24">
                            <div class="form-group">
                                <label class="form-label">Edad Gestacional (semanas)</label>
                                <input type="text" name="pediatric_history[gestational_age]" class="form-control-modern" value="{{ $record->pediatric_history['gestational_age'] ?? '' }}" placeholder="Ej. 38 semanas">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Peso al Nacer (g)</label>
                                <input type="text" name="pediatric_history[birth_weight]" class="form-control-modern" value="{{ $record->pediatric_history['birth_weight'] ?? '' }}" placeholder="Ej. 3100g">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Talla al Nacer (cm)</label>
                                <input type="text" name="pediatric_history[birth_height]" class="form-control-modern" value="{{ $record->pediatric_history['birth_height'] ?? '' }}" placeholder="Ej. 50cm">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Puntaje Apgar</label>
                                <input type="text" name="pediatric_history[apgar]" class="form-control-modern" value="{{ $record->pediatric_history['apgar'] ?? '' }}" placeholder="Ej. 9/10">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tipo de Parto</label>
                                <select name="pediatric_history[delivery_type]" class="form-control-modern">
                                    <option value="">Seleccionar...</option>
                                    <option value="vaginal" {{ ($record->pediatric_history['delivery_type'] ?? '') === 'vaginal' ? 'selected' : '' }}>Vaginal / Eutócico</option>
                                    <option value="cesarea" {{ ($record->pediatric_history['delivery_type'] ?? '') === 'cesarea' ? 'selected' : '' }}>Cesárea</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Hospitalización Neonatal (días/motivo)</label>
                                <input type="text" name="pediatric_history[neonatal_hospitalization]" class="form-control-modern" value="{{ $record->pediatric_history['neonatal_hospitalization'] ?? '' }}" placeholder="Ej. Ninguna / 3 días por ictericia">
                            </div>
                            <div class="form-group" style="grid-column: span 3;">
                                <label class="form-label">Complicaciones en Embarazo/Parto</label>
                                <textarea name="pediatric_history[complications]" class="form-control-modern" rows="2" spellcheck="true" placeholder="Describa si hubo preeclampsia, cordón en cuello, etc.">{{ $record->pediatric_history['complications'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <h4 style="margin:0 0 12px; font-size:14px; font-weight:600; color:var(--text-secondary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">Hitos del Desarrollo Psicomotor (Edad aproximada)</h4>
                        <div class="grid grid-3 mb-24">
                            <div class="form-group">
                                <label class="form-label">Control Cefálico</label>
                                <input type="text" name="pediatric_history[milestone_head_control]" class="form-control-modern" value="{{ $record->pediatric_history['milestone_head_control'] ?? '' }}" placeholder="Ej. 3 meses">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sedestación (Sentado)</label>
                                <input type="text" name="pediatric_history[milestone_sitting]" class="form-control-modern" value="{{ $record->pediatric_history['milestone_sitting'] ?? '' }}" placeholder="Ej. 6 meses">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gateo</label>
                                <input type="text" name="pediatric_history[milestone_crawling]" class="form-control-modern" value="{{ $record->pediatric_history['milestone_crawling'] ?? '' }}" placeholder="Ej. 8 meses">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bipedestación (Parado)</label>
                                <input type="text" name="pediatric_history[milestone_standing]" class="form-control-modern" value="{{ $record->pediatric_history['milestone_standing'] ?? '' }}" placeholder="Ej. 10 meses">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Marcha (Caminar)</label>
                                <input type="text" name="pediatric_history[milestone_walking]" class="form-control-modern" value="{{ $record->pediatric_history['milestone_walking'] ?? '' }}" placeholder="Ej. 12 meses">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Desarrollo del Lenguaje</label>
                                <input type="text" name="pediatric_history[language_development]" class="form-control-modern" value="{{ $record->pediatric_history['language_development'] ?? '' }}" placeholder="Ej. Monosílabas a los 10m">
                            </div>
                        </div>

                        <h4 style="margin:0 0 12px; font-size:14px; font-weight:600; color:var(--text-secondary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">Otros Antecedentes</h4>
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label class="form-label">Esquema de Vacunación</label>
                                <select name="pediatric_history[vaccinations]" class="form-control-modern">
                                    <option value="">Seleccionar...</option>
                                    <option value="completo" {{ ($record->pediatric_history['vaccinations'] ?? '') === 'completo' ? 'selected' : '' }}>Completo para la edad</option>
                                    <option value="incompleto" {{ ($record->pediatric_history['vaccinations'] ?? '') === 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Reflejos / Tono Muscular</label>
                                <input type="text" name="pediatric_history[reflexes]" class="form-control-modern" value="{{ $record->pediatric_history['reflexes'] ?? '' }}" placeholder="Ej. Reflejos primitivos integrados">
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">Observaciones Adicionales del Desarrollo</label>
                                <textarea name="pediatric_history[other_development]" class="form-control-modern" rows="2" spellcheck="true" placeholder="Otras observaciones específicas...">{{ $record->pediatric_history['other_development'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ ANT. PERSONALES NO PATOLÓGICOS (Sección 4) ═══ --}}
                @php $npat = array_merge(\App\Models\MedicalRecord::personalNonPathologicalDefaults(), $record->personal_non_pathological ?? []); @endphp
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>Antecedentes Personales No Patológicos</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Lugar de Nacimiento</label>
                            <input type="text" name="personal_non_pathological[lugar_nacimiento]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.lugar_nacimiento', $npat['lugar_nacimiento']) }}" placeholder="Lugar + Zona Urbana/Suburbana/Rural">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Residencia Actual</label>
                            <input type="text" name="personal_non_pathological[residencia_actual]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.residencia_actual', $npat['residencia_actual']) }}" placeholder="Lugar + Zona Urbana/Suburbana/Rural">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Casa Habitación</label>
                            <input type="text" name="personal_non_pathological[casa_habitacion]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.casa_habitacion', $npat['casa_habitacion']) }}" placeholder="Propia, Rentada, Compartida, Barreras arq.">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transporte</label>
                            <input type="text" name="personal_non_pathological[transporte]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.transporte', $npat['transporte']) }}" placeholder="Propio, Público, Mixto, Dependiente">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nivel Socioeconómico</label>
                            <input type="text" name="personal_non_pathological[nivel_socioeconomico]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.nivel_socioeconomico', $npat['nivel_socioeconomico']) }}" placeholder="Bajo, Medio, Medio-alto, Alto">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Animales Dom&eacute;sticos</label>
                            <input type="text" name="personal_non_pathological[animales_domesticos]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.animales_domesticos', $npat['animales_domesticos']) }}" placeholder="Perro, Gato">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Complexi&oacute;n F&iacute;sica</label>
                            <select name="personal_non_pathological[complexion_fisica]" class="form-control-modern">
                                <option value="">Seleccionar...</option>
                                <option value="Delgada" {{ old('personal_non_pathological.complexion_fisica', $npat['complexion_fisica'] ?? '') === 'Delgada' ? 'selected' : '' }}>Delgada</option>
                                <option value="Mediana" {{ old('personal_non_pathological.complexion_fisica', $npat['complexion_fisica'] ?? '') === 'Mediana' ? 'selected' : '' }}>Mediana</option>
                                <option value="Robusta" {{ old('personal_non_pathological.complexion_fisica', $npat['complexion_fisica'] ?? '') === 'Robusta' ? 'selected' : '' }}>Robusta</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nivel de Estr&eacute;s</label>
                            <input type="text" name="personal_non_pathological[nivel_estres]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.nivel_estres', $npat['nivel_estres'] ?? '') }}" placeholder="Ej: 0-10, Bajo, Medio...">
                            <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:4px;">Sugerencias: Bajo, Medio, Alto, 0-10</span>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Exigencia Laboral</label>
                            <input type="text" name="personal_non_pathological[exigencia_laboral]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.exigencia_laboral', $npat['exigencia_laboral'] ?? '') }}" placeholder="Ej: Moderada, Pesada...">
                            <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:4px;">Sugerencias: Sedentaria, Ligera, Moderada, Pesada, Repetitiva, Mixta</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dieta</label>
                            <input type="text" name="personal_non_pathological[dieta]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.dieta', $npat['dieta']) }}" placeholder="Balanceada, Alta carbohidratos, Irregular...">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">AVD (Actividades de la Vida Diaria)</label>
                            <input type="text" name="personal_non_pathological[avd]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.avd', $npat['avd']) }}" placeholder="Independiente, Parcialmente dependiente, Dependiente">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado de Salud</label>
                            <input type="text" name="personal_non_pathological[estado_salud]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.estado_salud', $npat['estado_salud']) }}" placeholder="Bueno, Regular, Malo">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Hábitos de Sueño</label>
                            <input type="text" name="personal_non_pathological[habitos_sueno]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.habitos_sueno', $npat['habitos_sueno']) }}" placeholder="7-8 hrs, <6 hrs, Sueño no reparador, Insomnio">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Otros</label>
                            <input type="text" name="personal_non_pathological[otros]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.otros', $npat['otros']) }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ ANTECEDENTES HEREDOFAMILIARES (Sección 3) ═══ --}}
                @php $fh = array_merge(\App\Models\MedicalRecord::familyHistoryDefaults(), $record->family_history ?? []); @endphp
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Antecedentes Heredofamiliares</h3>
                        <small style="color:var(--text-muted); display:block; width:100%; margin-top:4px;">(Incluir información de abuelos, padres, hermanos o hijos)</small>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Diabetes</label>
                            <input type="text" name="family_history[diabetes]" class="form-control-modern"
                                value="{{ old('family_history.diabetes', $fh['diabetes']) }}" placeholder="DM1, DM2">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cardíacas</label>
                            <input type="text" name="family_history[cardiacas]" class="form-control-modern"
                                value="{{ old('family_history.cardiacas', $fh['cardiacas']) }}" placeholder="HT, Infarto, Arritmias, Enf. coronaria">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vasculares</label>
                            <input type="text" name="family_history[vasculares]" class="form-control-modern"
                                value="{{ old('family_history.vasculares', $fh['vasculares']) }}" placeholder="Várices, Trombosis, Insuf. venosa">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pulmonares</label>
                            <input type="text" name="family_history[pulmonares]" class="form-control-modern"
                                value="{{ old('family_history.pulmonares', $fh['pulmonares']) }}" placeholder="Asma, EPOC, Bronquitis, Fibrosis">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Neoplasias</label>
                            <input type="text" name="family_history[neoplasias]" class="form-control-modern"
                                value="{{ old('family_history.neoplasias', $fh['neoplasias']) }}" placeholder="Cáncer de mama, colon, pulmonar">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Metab&oacute;licos</label>
                            <input type="text" name="family_history[metabolicos]" class="form-control-modern"
                                value="{{ old('family_history.metabolicos', $fh['metabolicos'] ?? $fh['epilepsia'] ?? '') }}" placeholder="Obesidad, Hipotiroidismo, Dislipidemia">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Síndromes</label>
                            <input type="text" name="family_history[sindromes]" class="form-control-modern"
                                value="{{ old('family_history.sindromes', $fh['sindromes']) }}" placeholder="Sx Down, Marfan, TEA, Ehlers-Danlos">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sistema Nervioso</label>
                            <input type="text" name="family_history[sistema_nervioso]" class="form-control-modern"
                                value="{{ old('family_history.sistema_nervioso', $fh['sistema_nervioso']) }}" placeholder="Parkinson, Alzheimer, Epilepsia, EVC">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Autoinmunes</label>
                            <input type="text" name="family_history[autoinmunes]" class="form-control-modern"
                                value="{{ old('family_history.autoinmunes', $fh['autoinmunes']) }}" placeholder="Artritis, Lupus, Esclerosis, Fibromialgia">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Otros</label>
                            <input type="text" name="family_history[otros]" class="form-control-modern"
                                value="{{ old('family_history.otros', $fh['otros']) }}" placeholder="Sí (Especificar)">
                        </div>
                    </div>
                </div>

                {{-- ═══ ANT. PERSONALES PATOLÓGICOS (Sección 5) ═══ --}}
                @php $pat = array_merge(\App\Models\MedicalRecord::personalPathologicalDefaults(), $record->personal_pathological ?? []); @endphp
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-notes-medical"></i>
                        <h3>Antecedentes Personales Patológicos</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Hospitalización</label>
                            <input type="text" name="personal_pathological[hospitalizacion]" class="form-control-modern"
                                value="{{ old('personal_pathological.hospitalizacion', $pat['hospitalizacion']) }}" placeholder="Niega, Reciente (<1 año), Antigua (>1 año)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cirugías</label>
                            <input type="text" name="personal_pathological[cirugias]" class="form-control-modern"
                                value="{{ old('personal_pathological.cirugias', $pat['cirugias']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hábitos Tóxicos</label>
                            <input type="text" name="personal_pathological[habitos_toxicos]" class="form-control-modern"
                                value="{{ old('personal_pathological.habitos_toxicos', $pat['habitos_toxicos']) }}" placeholder="Tabaco, Alcohol, Drogas">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alergias</label>
                            <input type="text" name="personal_pathological[alergias]" class="form-control-modern"
                                value="{{ old('personal_pathological.alergias', $pat['alergias']) }}" placeholder="Medicamentos, Alimentos, Ambientales">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Traumatismos</label>
                            <input type="text" name="personal_pathological[traumatismos]" class="form-control-modern"
                                value="{{ old('personal_pathological.traumatismos', $pat['traumatismos']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Convulsiones</label>
                            <input type="text" name="personal_pathological[convulsiones]" class="form-control-modern"
                                value="{{ old('personal_pathological.convulsiones', $pat['convulsiones']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Neoplasias</label>
                            <input type="text" name="personal_pathological[neoplasias]" class="form-control-modern"
                                value="{{ old('personal_pathological.neoplasias', $pat['neoplasias']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hipertensión</label>
                            <input type="text" name="personal_pathological[hipertension]" class="form-control-modern"
                                value="{{ old('personal_pathological.hipertension', $pat['hipertension']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Diabetes</label>
                            <input type="text" name="personal_pathological[diabetes]" class="form-control-modern"
                                value="{{ old('personal_pathological.diabetes', $pat['diabetes']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reumatológicos</label>
                            <input type="text" name="personal_pathological[reumatologicos]" class="form-control-modern"
                                value="{{ old('personal_pathological.reumatologicos', $pat['reumatologicos']) }}" placeholder="Artritis, Lupus">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Psiquiátricos</label>
                            <input type="text" name="personal_pathological[psiquiatricos]" class="form-control-modern"
                                value="{{ old('personal_pathological.psiquiatricos', $pat['psiquiatricos']) }}" placeholder="Ansiedad, Depresión">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Enfermedades Congénitas</label>
                            <input type="text" name="personal_pathological[enfermedades_congenitas]"
                                class="form-control-modern"
                                value="{{ old('personal_pathological.enfermedades_congenitas', $pat['enfermedades_congenitas']) }}" placeholder="Cardíacas, Respiratorias, Neurológicas">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Medicamentos Actuales</label>
                            <input type="text" name="personal_pathological[medicamentos_actuales]"
                                class="form-control-modern"
                                value="{{ old('personal_pathological.medicamentos_actuales', $pat['medicamentos_actuales']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Otros</label>
                            <input type="text" name="personal_pathological[otros]" class="form-control-modern"
                                value="{{ old('personal_pathological.otros', $pat['otros']) }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ INTERROGATORIO POR APARATOS Y SISTEMAS (Sección 6) ═══ --}}
                @php $sr = array_merge(\App\Models\MedicalRecord::systemsReviewDefaults(), $record->systems_review ?? []); @endphp
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-stethoscope"></i>
                        <h3>Interrogatorio por Aparatos y Sistemas</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Alteraciones Visuales</label>
                            <input type="text" name="systems_review[visual]" class="form-control-modern"
                                value="{{ old('systems_review.visual', $sr['visual']) }}" placeholder="Visión borrosa, Miopía, Diplopía, Pérdida visual, Hemianopsia">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones Auditivas</label>
                            <input type="text" name="systems_review[auditivo]" class="form-control-modern"
                                value="{{ old('systems_review.auditivo', $sr['auditivo']) }}" placeholder="Hipoacusia, Tinnitus, Vértigo">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones Sensoriales</label>
                            <input type="text" name="systems_review[sensorial]" class="form-control-modern"
                                value="{{ old('systems_review.sensorial', $sr['sensorial']) }}" placeholder="Parestesias, Hipoestesia, Astereognosia, Alteración térmica...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Musculoesquelético</label>
                            <input type="text" name="systems_review[musculoesqueletico]" class="form-control-modern"
                                value="{{ old('systems_review.musculoesqueletico', $sr['musculoesqueletico']) }}" placeholder="Dolor localizado, Debilidad muscular, Limitación">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones del S.N.</label>
                            <input type="text" name="systems_review[sistema_nervioso]" class="form-control-modern"
                                value="{{ old('systems_review.sistema_nervioso', $sr['sistema_nervioso']) }}" placeholder="Cefalea frecuente, Pérdida de memoria, Migraña, Insomnio">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Metabólico</label>
                            <input type="text" name="systems_review[metabolico]" class="form-control-modern"
                                value="{{ old('systems_review.metabolico', $sr['metabolico']) }}" placeholder="Tiroides, Crecimiento, Obesidad, Sed, Intolerancia frío/calor">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Respiratorio</label>
                            <input type="text" name="systems_review[respiratorio]" class="form-control-modern"
                                value="{{ old('systems_review.respiratorio', $sr['respiratorio']) }}" placeholder="Disnea, disfonía, tos, síncope, COVID">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cardíaco</label>
                            <input type="text" name="systems_review[cardiaco]" class="form-control-modern"
                                value="{{ old('systems_review.cardiaco', $sr['cardiaco']) }}" placeholder="Dolor torácico, Trombosis, Vasomotoras, Cianosis, Edema">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gastrointestinal</label>
                            <input type="text" name="systems_review[gastrointestinal]" class="form-control-modern"
                                value="{{ old('systems_review.gastrointestinal', $sr['gastrointestinal']) }}" placeholder="Dolor abdominal, Náuseas, Reflujo, Estreñimiento, Colon irritable">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Genitourinario</label>
                            <input type="text" name="systems_review[genitourinario]" class="form-control-modern"
                                value="{{ old('systems_review.genitourinario', $sr['genitourinario']) }}" placeholder="Dolor pélvico, Incontinencia, Alteraciones menstruales, Dolor mamario">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Piel</label>
                            <input type="text" name="systems_review[piel]" class="form-control-modern"
                                value="{{ old('systems_review.piel', $sr['piel']) }}" placeholder="Cicatrices, Lesiones, Cambios de coloración">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Otros</label>
                            <input type="text" name="systems_review[otros]" class="form-control-modern"
                                value="{{ old('systems_review.otros', $sr['otros']) }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ OBSERVACIONES (Sección 2) ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-comment-medical"></i>
                        <h3>Observaciones</h3>
                    </div>
                    <div class="form-group">
                        <textarea name="observations" class="form-control-modern" spellcheck="true"
                            rows="4">{{ old('observations', $record->observations) }}</textarea>
                    </div>
                </div>

                {{-- ═══ SAVE BUTTON ═══ --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:32px;">
                    <button type="submit" id="medical-record-submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Historia Clínica
                    </button>
                </div>
            </form>
            </div>
            </div>
        @endif
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

<div>
    {{-- ═══ PATIENT HEADER ═══ --}}
    <div class="glass-card mb-24" wire:key="patient-header">
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
                        &nbsp;·&nbsp;
                        @if($patient->birth_date)
                            {{ $patient->age }} años
                        @endif
                        &nbsp;·&nbsp;
                        <span class="badge-modern badge-{{ $patient->status }}">
                            {{ $patient->status === 'active' ? 'Activo' : ($patient->status === 'inactive' ? 'Inactivo' : 'Alta') }}
                        </span>
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
                <a href="{{ route('admin.patients.index') }}" class="btn-modern btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
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
        <button class="tab-item {{ $activeTab === 'expediente' ? 'active' : '' }}" wire:click="setTab('expediente')">
            <i class="fas fa-file-medical"></i> Expediente
        </button>
        <button class="tab-item {{ $activeTab === 'consultas' ? 'active' : '' }}" wire:click="setTab('consultas')">
            <i class="fas fa-stethoscope"></i> Consultas ({{ $patient->consultations->count() }})
        </button>
        <button class="tab-item {{ $activeTab === 'pagos' ? 'active' : '' }}" wire:click="setTab('pagos')">
            <i class="fas fa-credit-card"></i> Pagos ({{ $patient->payments->count() }})
        </button>
        <button class="tab-item {{ $activeTab === 'historial' ? 'active' : '' }}" wire:click="setTab('historial')">
            <i class="fas fa-history"></i> Historial
        </button>
    </div>

    {{-- ═══ TAB: EXPEDIENTE ═══ --}}
    <div wire:key="tab-wrapper-expediente" style="display: {{ $activeTab === 'expediente' ? 'block' : 'none' }};">
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
            <form action="{{ route('admin.medical-records.update', $record) }}" method="POST">
                @csrf @method('PUT')

                {{-- ═══ DATOS PERSONALES (Sección 0) ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-user"></i>
                        <h3>Datos Personales</h3>
                    </div>
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Edad</label>
                            <input type="number" name="age" class="form-control-modern"
                                value="{{ old('age', $record->age) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado Civil</label>
                            <input type="text" name="marital_status" class="form-control-modern"
                                value="{{ old('marital_status', $record->marital_status) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Domicilio</label>
                            <input type="text" name="address" class="form-control-modern"
                                value="{{ old('address', $record->address) }}">
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
                                value="{{ old('occupation', $record->occupation) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dependencia Económica</label>
                            <input type="text" name="economic_dependency" class="form-control-modern"
                                value="{{ old('economic_dependency', $record->economic_dependency) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cuidador Primario</label>
                            <input type="text" name="primary_caregiver" class="form-control-modern"
                                value="{{ old('primary_caregiver', $record->primary_caregiver) }}">
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
                                value="{{ old('personal_non_pathological.referencia_como_entero', $record->personal_non_pathological['referencia_como_entero'] ?? '') }}">
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
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="personal_non_pathological[emergencia_nombre]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_nombre', $record->personal_non_pathological['emergencia_nombre'] ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tel&eacute;fono</label>
                            <input type="text" name="personal_non_pathological[emergencia_telefono]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_telefono', $record->personal_non_pathological['emergencia_telefono'] ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Parentesco</label>
                            <input type="text" name="personal_non_pathological[emergencia_parentesco]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.emergencia_parentesco', $record->personal_non_pathological['emergencia_parentesco'] ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- ═══ REPRESENTANTE LEGAL (Sección 1) ═══ --}}
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-users"></i>
                        <h3>Representante Legal</h3>
                    </div>
                    <div class="grid grid-3">
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

                {{-- ═══ ANTECEDENTES HEREDOFAMILIARES (Sección 3) ═══ --}}
                @php $fh = array_merge(\App\Models\MedicalRecord::familyHistoryDefaults(), $record->family_history ?? []); @endphp
                <div class="glass-card mb-24">
                    <div class="section-header">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Antecedentes Heredofamiliares</h3>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Diabetes</label>
                            <input type="text" name="family_history[diabetes]" class="form-control-modern"
                                value="{{ old('family_history.diabetes', $fh['diabetes']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cardíacas</label>
                            <input type="text" name="family_history[cardiacas]" class="form-control-modern"
                                value="{{ old('family_history.cardiacas', $fh['cardiacas']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vasculares</label>
                            <input type="text" name="family_history[vasculares]" class="form-control-modern"
                                value="{{ old('family_history.vasculares', $fh['vasculares']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pulmonares</label>
                            <input type="text" name="family_history[pulmonares]" class="form-control-modern"
                                value="{{ old('family_history.pulmonares', $fh['pulmonares']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Neoplasias</label>
                            <input type="text" name="family_history[neoplasias]" class="form-control-modern"
                                value="{{ old('family_history.neoplasias', $fh['neoplasias']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Epilepsia</label>
                            <input type="text" name="family_history[epilepsia]" class="form-control-modern"
                                value="{{ old('family_history.epilepsia', $fh['epilepsia']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Síndromes</label>
                            <input type="text" name="family_history[sindromes]" class="form-control-modern"
                                value="{{ old('family_history.sindromes', $fh['sindromes']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sistema Nervioso</label>
                            <input type="text" name="family_history[sistema_nervioso]" class="form-control-modern"
                                value="{{ old('family_history.sistema_nervioso', $fh['sistema_nervioso']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Autoinmunes</label>
                            <input type="text" name="family_history[autoinmunes]" class="form-control-modern"
                                value="{{ old('family_history.autoinmunes', $fh['autoinmunes']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Otros</label>
                            <input type="text" name="family_history[otros]" class="form-control-modern"
                                value="{{ old('family_history.otros', $fh['otros']) }}">
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
                                value="{{ old('personal_non_pathological.lugar_nacimiento', $npat['lugar_nacimiento']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Residencia Actual</label>
                            <input type="text" name="personal_non_pathological[residencia_actual]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.residencia_actual', $npat['residencia_actual']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Casa Habitación</label>
                            <input type="text" name="personal_non_pathological[casa_habitacion]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.casa_habitacion', $npat['casa_habitacion']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Transporte</label>
                            <input type="text" name="personal_non_pathological[transporte]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.transporte', $npat['transporte']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nivel Socioeconómico</label>
                            <input type="text" name="personal_non_pathological[nivel_socioeconomico]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.nivel_socioeconomico', $npat['nivel_socioeconomico']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Animales Domésticos</label>
                            <input type="text" name="personal_non_pathological[animales_domesticos]"
                                class="form-control-modern"
                                value="{{ old('personal_non_pathological.animales_domesticos', $npat['animales_domesticos']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dieta</label>
                            <input type="text" name="personal_non_pathological[dieta]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.dieta', $npat['dieta']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">AVD (Actividades de la Vida Diaria)</label>
                            <input type="text" name="personal_non_pathological[avd]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.avd', $npat['avd']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado de Salud</label>
                            <input type="text" name="personal_non_pathological[estado_salud]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.estado_salud', $npat['estado_salud']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hábitos de Sueño</label>
                            <input type="text" name="personal_non_pathological[habitos_sueno]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.habitos_sueno', $npat['habitos_sueno']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Otros</label>
                            <input type="text" name="personal_non_pathological[otros]" class="form-control-modern"
                                value="{{ old('personal_non_pathological.otros', $npat['otros']) }}">
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
                                value="{{ old('personal_pathological.hospitalizacion', $pat['hospitalizacion']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cirugías</label>
                            <input type="text" name="personal_pathological[cirugias]" class="form-control-modern"
                                value="{{ old('personal_pathological.cirugias', $pat['cirugias']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hábitos Tóxicos</label>
                            <input type="text" name="personal_pathological[habitos_toxicos]" class="form-control-modern"
                                value="{{ old('personal_pathological.habitos_toxicos', $pat['habitos_toxicos']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alergias</label>
                            <input type="text" name="personal_pathological[alergias]" class="form-control-modern"
                                value="{{ old('personal_pathological.alergias', $pat['alergias']) }}">
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
                                value="{{ old('personal_pathological.reumatologicos', $pat['reumatologicos']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Psiquiátricos</label>
                            <input type="text" name="personal_pathological[psiquiatricos]" class="form-control-modern"
                                value="{{ old('personal_pathological.psiquiatricos', $pat['psiquiatricos']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Enfermedades Congénitas</label>
                            <input type="text" name="personal_pathological[enfermedades_congenitas]"
                                class="form-control-modern"
                                value="{{ old('personal_pathological.enfermedades_congenitas', $pat['enfermedades_congenitas']) }}">
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
                                value="{{ old('systems_review.visual', $sr['visual']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones Auditivas</label>
                            <input type="text" name="systems_review[auditivo]" class="form-control-modern"
                                value="{{ old('systems_review.auditivo', $sr['auditivo']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones Sensoriales</label>
                            <input type="text" name="systems_review[sensorial]" class="form-control-modern"
                                value="{{ old('systems_review.sensorial', $sr['sensorial']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Musculoesquelético</label>
                            <input type="text" name="systems_review[musculoesqueletico]" class="form-control-modern"
                                value="{{ old('systems_review.musculoesqueletico', $sr['musculoesqueletico']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alteraciones del S.N.</label>
                            <input type="text" name="systems_review[sistema_nervioso]" class="form-control-modern"
                                value="{{ old('systems_review.sistema_nervioso', $sr['sistema_nervioso']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Metabólico</label>
                            <input type="text" name="systems_review[metabolico]" class="form-control-modern"
                                value="{{ old('systems_review.metabolico', $sr['metabolico']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Respiratorio</label>
                            <input type="text" name="systems_review[respiratorio]" class="form-control-modern"
                                value="{{ old('systems_review.respiratorio', $sr['respiratorio']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cardiaco</label>
                            <input type="text" name="systems_review[cardiaco]" class="form-control-modern"
                                value="{{ old('systems_review.cardiaco', $sr['cardiaco']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gastrointestinal</label>
                            <input type="text" name="systems_review[gastrointestinal]" class="form-control-modern"
                                value="{{ old('systems_review.gastrointestinal', $sr['gastrointestinal']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Genitourinario</label>
                            <input type="text" name="systems_review[genitourinario]" class="form-control-modern"
                                value="{{ old('systems_review.genitourinario', $sr['genitourinario']) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Piel</label>
                            <input type="text" name="systems_review[piel]" class="form-control-modern"
                                value="{{ old('systems_review.piel', $sr['piel']) }}">
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
                        <textarea name="observations" class="form-control-modern"
                            rows="4">{{ old('observations', $record->observations) }}</textarea>
                    </div>
                </div>

                {{-- ═══ SAVE BUTTON ═══ --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:32px;">
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Historia Clínica
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- ═══ TAB: CONSULTAS ═══ --}}
    <div wire:key="tab-wrapper-consultas" style="display: {{ $activeTab === 'consultas' ? 'block' : 'none' }};">
        <div class="flex-between mb-16" wire:key="consultas-header">
            <div style="font-size:14px;color:var(--text-secondary);">
                {{ $patient->consultations->count() }} consulta(s) registrada(s)
            </div>
            <button class="btn-modern btn-primary btn-sm" wire:click="toggleConsultationForm">
                <i class="fas fa-plus"></i> Nueva Consulta
            </button>
        </div>

        {{-- New Consultation Form --}}
        @if($showConsultationForm)
            <div class="glass-card mb-24" wire:key="new-consultation-form">
                <div class="card-header-custom">
                    <div class="card-title"><i class="fas fa-notes-medical"></i> Nueva Consulta</div>
                </div>
                <form action="{{ route('admin.consultations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    {{-- Motivo de Consulta --}}
                    <div class="form-group">
                        <label class="form-label">Motivo de Consulta *</label>
                        <textarea name="reason" class="form-control-modern" rows="2" required
                            placeholder="Descripción del motivo de consulta"></textarea>
                    </div>

                    {{-- Padecimiento Actual --}}
                    <details class="consultation-section" open>
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                            <i class="fas fa-heartbeat"></i>
                            <h3 style="display:inline;">Padecimiento Actual</h3>
                            <i class="fas fa-chevron-down"
                                style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div class="grid grid-2" style="margin-top:16px;">
                            <div class="form-group">
                                <label class="form-label">Inicio</label>
                                <input type="text" name="current_condition[inicio]" class="form-control-modern"
                                    placeholder="¿Cuándo inició el padecimiento?">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Mecanismo de Lesión</label>
                                <input type="text" name="current_condition[mecanismo_lesion]" class="form-control-modern"
                                    placeholder="¿Cómo ocurrió?">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Evolución</label>
                                <input type="text" name="current_condition[evolucion]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tratamiento Actual</label>
                                <input type="text" name="current_condition[tratamiento_actual]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Limitación del Movimiento</label>
                                <input type="text" name="current_condition[limitacion_movimiento]"
                                    class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">EVA (Escala Visual Analógica)</label>
                                <input type="text" name="current_condition[eva]" class="form-control-modern"
                                    placeholder="0-10">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fuerza</label>
                                <input type="text" name="current_condition[fuerza]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Actividad Física</label>
                                <input type="text" name="current_condition[actividad_fisica]" class="form-control-modern">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Comportamiento de los Síntomas (día/noche/parado/sentado)</label>
                                <textarea name="current_condition[comportamiento_sintomas]" class="form-control-modern"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pérdida/Ganancia de Peso Repentina</label>
                                <input type="text" name="current_condition[cambio_peso]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Cambios en el Apetito</label>
                                <input type="text" name="current_condition[cambios_apetito]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Debilidad o Astenia</label>
                                <input type="text" name="current_condition[debilidad_astenia]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Incontinencia Fecal/Urinaria</label>
                                <input type="text" name="current_condition[incontinencia]" class="form-control-modern">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Otros (hematoma, postura, marcha, signos de inflamación)</label>
                                <textarea name="current_condition[otros]" class="form-control-modern" rows="2"></textarea>
                            </div>
                        </div>
                    </details>

                    {{-- Exploración Física --}}
                    <details class="consultation-section" style="margin-top:16px;">
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                            <i class="fas fa-weight"></i>
                            <h3 style="display:inline;">Exploración Física</h3>
                            <i class="fas fa-chevron-down"
                                style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div class="grid grid-3" style="margin-top:16px;">
                            <div class="form-group">
                                <label class="form-label">Temperatura</label>
                                <input type="text" name="physical_exam[temperatura]" class="form-control-modern"
                                    placeholder="°C">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Frecuencia Cardíaca</label>
                                <input type="text" name="physical_exam[frecuencia_cardiaca]" class="form-control-modern"
                                    placeholder="lpm">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tensión Arterial</label>
                                <input type="text" name="physical_exam[tension_arterial]" class="form-control-modern"
                                    placeholder="mmHg">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Frecuencia Respiratoria</label>
                                <input type="text" name="physical_exam[frecuencia_respiratoria]" class="form-control-modern"
                                    placeholder="rpm">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Saturación de Oxígeno</label>
                                <input type="text" name="physical_exam[saturacion_oxigeno]" class="form-control-modern"
                                    placeholder="%">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Peso (kg)</label>
                                <input type="text" name="physical_exam[peso]" class="form-control-modern" placeholder="kg">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Altura (cm)</label>
                                <input type="text" name="physical_exam[altura]" class="form-control-modern"
                                    placeholder="cm">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Traslado</label>
                                <input type="text" name="physical_exam[traslado]" class="form-control-modern">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tipo de Marcha</label>
                                <input type="text" name="physical_exam[tipo_marcha]" class="form-control-modern">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Pruebas Complementarias (goniometría, Asia, Godet, pruebas
                                    musculares)</label>
                                <textarea name="physical_exam[pruebas_complementarias]" class="form-control-modern"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Otros</label>
                                <textarea name="physical_exam[otros]" class="form-control-modern" rows="2"></textarea>
                            </div>
                        </div>
                    </details>

                    {{-- Diagnóstico y Plan de Tratamiento --}}
                    <details class="consultation-section" style="margin-top:16px;">
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                            <i class="fas fa-clipboard-check"></i>
                            <h3 style="display:inline;">Diagnóstico y Plan de Tratamiento</h3>
                            <i class="fas fa-chevron-down"
                                style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div style="margin-top:16px;">
                            <div class="form-group">
                                <label class="form-label">Notas de Estudios Previos</label>
                                <textarea name="previous_studies_notes" class="form-control-modern" rows="2"
                                    placeholder="Estudios de imagen previos, laboratorios, etc."></textarea>
                            </div>
                            <div class="grid grid-2">
                                <div class="form-group">
                                    <label class="form-label">Diagnóstico Fisioterapéutico</label>
                                    <textarea name="diagnosis" class="form-control-modern" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pronóstico</label>
                                    <textarea name="prognosis" class="form-control-modern" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Objetivos de Tratamiento</label>
                                <textarea name="treatment_objectives" class="form-control-modern" rows="2"></textarea>
                            </div>
                        </div>
                    </details>

                    {{-- Alta Fisioterapéutica --}}
                    <details class="consultation-section" style="margin-top:16px;">
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                            <i class="fas fa-door-open"></i>
                            <h3 style="display:inline;">Alta Fisioterapéutica</h3>
                            <i class="fas fa-chevron-down"
                                style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div class="grid grid-2" style="margin-top:16px;">
                            <div class="form-group">
                                <label class="form-label">Motivo de Alta</label>
                                <textarea name="discharge_reason" class="form-control-modern" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Recomendaciones al Alta</label>
                                <textarea name="discharge_recommendations" class="form-control-modern" rows="2"></textarea>
                            </div>
                        </div>
                    </details>

                    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                        <button type="button" class="btn-modern btn-secondary btn-sm"
                            wire:click="toggleConsultationForm">Cancelar</button>
                        <button type="submit" class="btn-modern btn-primary btn-sm">
                            <i class="fas fa-save"></i> Guardar Consulta
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Consultations Grid --}}
        <div class="grid grid-2" wire:key="consultations-grid-container">
            @forelse ($patient->consultations as $consulta)
                <div class="consultation-card" wire:click="selectConsultation({{ $consulta->id }})"
                    wire:key="consultation-{{ $consulta->id }}">
                    <div class="flex-between mb-8">
                        <span class="badge-modern badge-{{ $consulta->status }}">
                            {{ $consulta->status === 'open' ? 'Activa' : ($consulta->status === 'in_progress' ? 'En curso' : 'Inactiva') }}
                        </span>
                        <span style="font-size:12px;color:var(--text-muted);">
                            {{ $consulta->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div style="font-weight:600;margin-bottom:4px;">{{ Str::limit($consulta->reason, 60) }}</div>
                    @if($consulta->diagnosis)
                        <div style="font-size:13px;color:var(--text-secondary);">{{ Str::limit($consulta->diagnosis, 80) }}
                        </div>
                    @endif
                    <div style="margin-top:10px;font-size:12px;color:var(--text-muted);">
                        <i class="fas fa-sticky-note"></i> {{ $consulta->evolutionNotes->count() }} nota(s)
                        &nbsp;·&nbsp;
                        <i class="fas fa-image"></i> {{ $consulta->studies->count() }} estudio(s)
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1;">
                    <div class="empty-state">
                        <i class="fas fa-stethoscope"></i>
                        <p>No hay consultas registradas</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ═══ CONSULTATION DETAIL SLIDE PANEL ═══ --}}
        <div wire:key="consultation-slide-panel-wrapper">
            @php $selected = $selectedConsultationId ? $patient->consultations->find($selectedConsultationId) : null; @endphp

            {{-- Overlay --}}
            <div class="slide-panel-overlay" style="display: {{ $selected ? 'block' : 'none' }};"
                wire:click="closeConsultation" wire:key="permanent-slide-panel-overlay"></div>

            {{-- Panel --}}
            <div class="slide-panel" style="transform: {{ $selected ? 'translateX(0)' : 'translateX(100%)' }};"
                wire:key="permanent-slide-panel">

                {{-- Static Top Container for Details --}}
                <div wire:key="slide-panel-top-container">
                    @if($selected)
                        <div class="flex-between mb-24">
                            <h3 style="margin:0;font-size:18px;font-weight:600;">
                                <i class="fas fa-file-medical-alt" style="color:var(--accent-primary)"></i> Detalle de
                                Consulta
                            </h3>
                            <button class="btn-icon" wire:click="closeConsultation">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="data-pair">
                            <div class="data-label">Motivo de Consulta</div>
                            <div class="data-value">{{ $selected->reason }}</div>
                        </div>

                        @if($selected->diagnosis)
                            <div class="data-pair">
                                <div class="data-label">Diagnóstico</div>
                                <div class="data-value">{{ $selected->diagnosis }}</div>
                            </div>
                        @endif

                        @if($selected->prognosis)
                            <div class="data-pair">
                                <div class="data-label">Pronóstico</div>
                                <div class="data-value">{{ $selected->prognosis }}</div>
                            </div>
                        @endif

                        @if($selected->treatment_objectives)
                            <div class="data-pair">
                                <div class="data-label">Objetivos de Tratamiento</div>
                                <div class="data-value">{{ $selected->treatment_objectives }}</div>
                            </div>
                        @endif

                        {{-- Current Condition JSON --}}
                        @if($selected->current_condition && collect($selected->current_condition)->filter()->count())
                            <details class="consultation-section" style="margin-top:16px;" open>
                                <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                                    <i class="fas fa-heartbeat"></i>
                                    <h3 style="display:inline;">Padecimiento Actual</h3>
                                    <i class="fas fa-chevron-down"
                                        style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                                </summary>
                                <div class="grid grid-2" style="margin-top:12px;">
                                    @foreach($selected->current_condition as $key => $value)
                                        @if($value)
                                            <div class="data-pair" wire:key="json-data-{{ $selected->id }}-{{ $key }}">
                                                <div class="data-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                <div class="data-value">{{ $value }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @endif

                        {{-- Physical Exam JSON --}}
                        @if($selected->physical_exam && collect($selected->physical_exam)->filter()->count())
                            <details class="consultation-section" style="margin-top:12px;">
                                <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                                    <i class="fas fa-weight"></i>
                                    <h3 style="display:inline;">Exploración Física</h3>
                                    <i class="fas fa-chevron-down"
                                        style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                                </summary>
                                <div class="grid grid-2" style="margin-top:12px;">
                                    @foreach($selected->physical_exam as $key => $value)
                                        @if($value)
                                            <div class="data-pair" wire:key="physical-exam-{{ $selected->id }}-{{ $key }}">
                                                <div class="data-label">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                                <div class="data-value">{{ $value }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @endif

                        {{-- Notas de Estudios Previos --}}
                        @if($selected->previous_studies_notes)
                            <div class="data-pair" style="margin-top:20px;">
                                <div class="data-label">Notas de Estudios Previos</div>
                                <div class="data-value">{{ $selected->previous_studies_notes }}</div>
                            </div>
                        @endif

                        {{-- Alta Fisioterapéutica --}}
                        @if($selected->discharge_reason || $selected->discharge_recommendations)
                            <details class="consultation-section" style="margin-top:12px;">
                                <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none;">
                                    <i class="fas fa-door-open"></i>
                                    <h3 style="display:inline;">Alta Fisioterapéutica</h3>
                                    <i class="fas fa-chevron-down"
                                        style="float:right; margin-top:4px; font-size:12px; color:var(--text-muted);"></i>
                                </summary>
                                <div style="margin-top:12px;">
                                    @if($selected->discharge_reason)
                                        <div class="data-pair">
                                            <div class="data-label">Motivo de Alta</div>
                                            <div class="data-value">{{ $selected->discharge_reason }}</div>
                                        </div>
                                    @endif
                                    @if($selected->discharge_recommendations)
                                        <div class="data-pair">
                                            <div class="data-label">Recomendaciones al Alta</div>
                                            <div class="data-value">{{ $selected->discharge_recommendations }}</div>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        @endif
                    @else
                        <div style="padding: 20px;">Seleccione una consulta para ver sus detalles.</div>
                    @endif
                </div>

                {{-- Sub-Navigation for Panel --}}
                <div class="tabs-modern"
                    style="margin: 20px 0; padding: 4px; background: rgba(255,255,255,0.03); border-radius: 12px;"
                    wire:key="panel-sub-tabs">
                    <button class="tab-item {{ $consultationTab === 'evolution' ? 'active' : '' }}"
                        wire:click="setConsultationTab('evolution')"
                        style="padding: 8px 16px; font-size: 13px; flex: 1; border-radius: 8px;">
                        <i class="fas fa-sticky-note"></i> Notas
                    </button>
                    <button class="tab-item {{ $consultationTab === 'studies' ? 'active' : '' }}"
                        wire:click="setConsultationTab('studies')"
                        style="padding: 8px 16px; font-size: 13px; flex: 1; border-radius: 8px;">
                        <i class="fas fa-image"></i> Estudios
                    </button>
                    <button class="tab-item {{ $consultationTab === 'exercises' ? 'active' : '' }}"
                        wire:click="setConsultationTab('exercises')"
                        style="padding: 8px 16px; font-size: 13px; flex: 1; border-radius: 8px;">
                        <i class="fas fa-running"></i> Ejercicios
                    </button>
                </div>

                {{-- Static Bottom Container for Livewire Component --}}
                <div style="margin-top:4px; display: {{ $selected ? 'block' : 'none' }};"
                    wire:key="consultation-components-wrapper">
                    @if($consultationTab === 'evolution')
                        <livewire:evolution-notes :consultationId="$selectedConsultationId"
                            wire:key="evolution-notes-{{ $selectedConsultationId ?? 'empty' }}" />
                    @elseif($consultationTab === 'studies')
                        <livewire:consultation-studies :consultationId="$selectedConsultationId"
                            wire:key="consultation-studies-{{ $selectedConsultationId ?? 'empty' }}" />
                    @else
                        <livewire:consultation-exercises :consultationId="$selectedConsultationId"
                            wire:key="consultation-exercises-{{ $selectedConsultationId ?? 'empty' }}" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TAB: PAGOS ═══ --}}
    <div wire:key="tab-wrapper-pagos" style="display: {{ $activeTab === 'pagos' ? 'block' : 'none' }};">
        <div class="flex-between mb-16">
            <div style="font-size:14px;color:var(--text-secondary);">
                {{ $patient->payments->count() }} pago(s) registrado(s)
            </div>
            <button class="btn-modern btn-primary btn-sm" wire:click="togglePaymentForm">
                <i class="fas fa-plus"></i> Registrar Pago
            </button>
        </div>

        {{-- New Payment Form --}}
        @if($showPaymentForm)
            <div class="glass-card mb-24" wire:key="new-payment-form">
                <form action="{{ route('admin.payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Fecha *</label>
                            <input type="date" name="date" class="form-control-modern" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">N° Sesión</label>
                            <input type="number" name="session_number" class="form-control-modern" min="1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Método de Pago</label>
                            <select name="payment_method" class="form-control-modern">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Total a Pagar *</label>
                            <input type="number" name="total_amount" class="form-control-modern" step="0.01" min="0"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Importe Pagado *</label>
                            <input type="number" name="amount_paid" class="form-control-modern" step="0.01" min="0"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Terapeuta</label>
                            <input type="text" name="therapist" class="form-control-modern"
                                value="{{ auth()->user()->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control-modern" rows="2"></textarea>
                    </div>
                    <div style="display:flex;gap:12px;justify-content:flex-end;">
                        <button type="button" class="btn-modern btn-secondary btn-sm"
                            wire:click="togglePaymentForm">Cancelar</button>
                        <button type="submit" class="btn-modern btn-primary btn-sm"><i class="fas fa-save"></i>
                            Guardar</button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Payments Table --}}
        @if($patient->payments->count() > 0)
            <div class="glass-card" style="padding:0; overflow:hidden;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Sesión</th>
                            <th>Método</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th>Terapeuta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient->payments->sortByDesc('date') as $pago)
                            <tr wire:key="payment-{{ $pago->id }}">
                                <td>{{ $pago->date->format('d/m/Y') }}</td>
                                <td>{{ $pago->session_number ?? '—' }}</td>
                                <td>{{ ucfirst($pago->payment_method ?? '—') }}</td>
                                <td style="font-weight:600;">${{ number_format($pago->total_amount, 2) }}</td>
                                <td style="color:var(--success);">${{ number_format($pago->amount_paid, 2) }}</td>
                                <td style="color:{{ $pago->balance > 0 ? 'var(--danger)' : 'var(--text-muted)' }};">
                                    ${{ number_format($pago->balance, 2) }}
                                </td>
                                <td>{{ $pago->therapist ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <p>No hay pagos registrados</p>
            </div>
        @endif
    </div>

    {{-- ═══ TAB: HISTORIAL ═══ --}}
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
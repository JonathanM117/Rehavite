{{--
    Campos del Expediente (rediseño basado en el HTML de referencia del cliente).
    Un solo partial usado tanto por la creación (admin/patients/create.blade.php)
    como por la edición (livewire/patient-detail.blade.php, modal "Editar
    Expediente"). Espera una variable opcional $patient (null al crear).
--}}
@php
    $cat = \App\Support\ExpedienteCatalogo::listas();
    $regionesCat = \App\Support\ExpedienteCatalogo::regiones();
    $antecedentesCat = \App\Support\ExpedienteCatalogo::antecedentes();
    $interrogatorioCat = \App\Support\ExpedienteCatalogo::interrogatorio();
    $listasRepetiblesCat = \App\Support\ExpedienteCatalogo::listasRepetibles();
    $opcionesCuando = \App\Support\ExpedienteCatalogo::opcionesCuando();
    $meses = \App\Support\ExpedienteCatalogo::meses();

    $p = $patient ?? null;
    $v = fn ($field, $default = null) => old($field, data_get($p, $field, $default));
    $therapists = $therapists ?? \App\Models\User::orderBy('name')->get();

    $mhm = array_replace_recursive(
        \App\Models\Patient::medicalHistoryMatrixDefaults(),
        old('medical_history_matrix', $p?->medical_history_matrix ?? [])
    );
    $sym = array_replace_recursive(
        \App\Models\Patient::symptomsReviewDefaults(),
        old('symptoms_review', $p?->symptoms_review ?? [])
    );
    $hosp = old('hospitalizations', $p?->hospitalizations ?? []);
    $cirug = old('surgeries', $p?->surgeries ?? []);
    $traum = old('traumas', $p?->traumas ?? []);
    $medic = old('current_medications', $p?->current_medications ?? []);

    $bodyRegions = old('body_regions', $p?->body_regions ?? []);
    $archBarriers = old('architectural_barriers', $p?->architectural_barriers ?? []);
    $petsChecked = old('pets', $p?->pets ?? []);
    $toxicHabits = old('toxic_habits', $p?->toxic_habits ?? []);
    $allergiesChecked = old('allergies', $p?->allergies ?? []);

    $hasAltContact = $v('alt_contact_name') || $v('alt_contact_phone');
    $isMinor = $p?->birth_date && $p->birth_date->age < 18;
@endphp

<script>
    function expedienteForm() {
        return {
            birthDate: @js($v('birth_date', '') ? \Carbon\Carbon::parse($v('birth_date'))->format('Y-m-d') : ''),
            regionesCat: @js($regionesCat),
            bodyRegions: @js($bodyRegions),
            diagnosisText: @js($v('diagnosis', '')),
            manualAge: @js($v('manual_age', '')),
            showAltContact: {{ $hasAltContact ? 'true' : 'false' }},
            altContactName: @js($v('alt_contact_name', '')),
            referralSource: @js($v('referral_source', '')),
            guardianRequired: {{ $v('guardian_required', $isMinor) ? 'true' : 'false' }},
            stressLevel: @js((string) $v('stress_level', '0')),

            get edad() {
                if (!this.birthDate) return null;
                const hoy = new Date();
                const nac = new Date(this.birthDate + 'T00:00:00');
                if (isNaN(nac.getTime())) return null;
                let edad = hoy.getFullYear() - nac.getFullYear();
                const m = hoy.getMonth() - nac.getMonth();
                if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
                return edad;
            },
            get esMenor() {
                return this.edad !== null && this.edad >= 0 && this.edad < 18;
            },

            toggleRegion(key) {
                const i = this.bodyRegions.indexOf(key);
                if (i >= 0) this.bodyRegions.splice(i, 1); else this.bodyRegions.push(key);
            },
            get fichasRegion() {
                const vistas = new Set();
                const fichas = [];
                this.bodyRegions.forEach(key => {
                    const region = this.regionesCat[key];
                    if (!region) return;
                    [...region.dx, ...region.qx].forEach(texto => {
                        if (!vistas.has(texto)) { vistas.add(texto); fichas.push(texto); }
                    });
                });
                return fichas;
            },
            toggleChipIn(obj, campo, texto) {
                let partes = (obj[campo] || '').split(',').map(s => s.trim()).filter(Boolean);
                const i = partes.indexOf(texto);
                if (i >= 0) partes.splice(i, 1); else partes.push(texto);
                obj[campo] = partes.join(', ');
            },
            chipActiva(obj, campo, texto) {
                return (obj[campo] || '').split(',').map(s => s.trim()).includes(texto);
            },
            copiarPersona(origen, destinoNombre, destinoParentesco, destinoTelefono) {
                if (destinoNombre) document.getElementById(destinoNombre).value = document.getElementById(origen + 'Nombre').value;
                if (destinoParentesco) document.getElementById(destinoParentesco).value = document.getElementById(origen + 'Parentesco').value;
                if (destinoTelefono) document.getElementById(destinoTelefono).value = document.getElementById(origen + 'Telefono').value;
            },
        };
    }
</script>

<div x-data="expedienteForm()" x-init="$watch('edad', (value) => { manualAge = value !== null ? value : ''; })">
    <input type="hidden" name="expediente_form" value="1">

    {{-- ═══ IDENTIFICACIÓN ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-user-plus"></i>
            <h3>Identificación</h3>
        </div>
        <div class="rejilla">
            <div class="campo w4">
                <label class="form-label">Nombre *</label>
                <input type="text" name="name" class="form-control-modern" value="{{ $v('name') }}" required>
            </div>
            <div class="campo w4">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="last_name" class="form-control-modern" value="{{ $v('last_name') }}" required>
            </div>
            <div class="campo w4">
                <label class="form-label">Correo Electrónico *</label>
                <input type="email" name="email" class="form-control-modern" value="{{ $v('email') }}" required>
            </div>
            <div class="campo w3">
                <label class="form-label">Fecha de Nacimiento *</label>
                <input type="date" name="birth_date" class="form-control-modern" x-model="birthDate" required>
            </div>
            <div class="campo w3">
                <label class="form-label">Género *</label>
                <select name="gender" class="form-control-modern" required>
                    <option value="no_especificado" {{ $v('gender') == 'no_especificado' ? 'selected' : '' }}>Prefiere no especificar</option>
                    <option value="femenino" {{ $v('gender') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="masculino" {{ $v('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="otro" {{ $v('gender') == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div class="campo w3">
                <label class="form-label">Edad <span class="opc">(se calcula sola, puedes corregirla)</span></label>
                <input type="number" name="manual_age" class="form-control-modern" x-model="manualAge" min="0" max="130">
            </div>
            <div class="campo w3">
                <div class="derivado" style="padding-top:26px;">
                    <span class="marca-menor" x-show="esMenor" x-cloak>Menor de edad</span>
                </div>
            </div>

            @if(auth()->user()->admin)
                <div class="campo w4">
                    <label class="form-label">Fisioterapeuta Asignado *</label>
                    <select name="user_id" class="form-control-modern" required>
                        @foreach($therapists as $therapist)
                            <option value="{{ $therapist->id }}" {{ ($v('user_id') == $therapist->id || (!$p && !old('user_id') && auth()->id() == $therapist->id)) ? 'selected' : '' }}>
                                {{ $therapist->name }} ({{ $therapist->admin ? 'Admin' : 'Fisio' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(!$p)
                    <div class="campo w4">
                        <label class="form-label">Fecha de Ingreso (Opcional)</label>
                        <input type="date" name="created_at" class="form-control-modern" value="{{ old('created_at') }}">
                    </div>
                @endif
            @endif
            @if($p)
                <div class="campo w4">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-control-modern">
                        @foreach(['activo'=>'Activo','seguimiento'=>'Seguimiento','alta_clinica'=>'Alta clínica','suspendido'=>'Suspendido','abandono'=>'Abandono terapéutico','referido'=>'Referido'] as $statusVal => $statusLabel)
                            <option value="{{ $statusVal }}" {{ $v('status') === $statusVal ? 'selected' : '' }}>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="campo w12">
                <label class="form-label">Región corporal</label>
                <div class="casillas">
                    @foreach($regionesCat as $key => $region)
                        <label class="casilla">
                            <input type="checkbox" name="body_regions[]" value="{{ $key }}" x-model="bodyRegions">
                            {{ $region['n'] }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="campo w12">
                <label class="form-label">Padecimiento / Diagnóstico principal</label>
                <textarea name="diagnosis" id="campo-diagnosis" class="form-control-modern" rows="3" x-model="diagnosisText"></textarea>
                <div class="fichas" x-show="fichasRegion.length" x-cloak>
                    <template x-for="chip in fichasRegion" :key="chip">
                        <button type="button" class="ficha" :class="chipActiva({diagnosisText}, 'diagnosisText', chip) ? 'activa' : ''"
                            @click="toggleChipIn($data, 'diagnosisText', chip)" x-text="chip"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ CONTACTO ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-phone-alt"></i>
            <h3>Contacto</h3>
        </div>
        <div class="rejilla">
            <div class="campo w4">
                <label class="form-label">Teléfono *</label>
                <input type="text" name="phone" id="contactoTelefono" class="form-control-modern" value="{{ $v('phone') }}" required>
            </div>
            @include('livewire.partials.catalog-select', ['name' => 'phone_owner', 'options' => $cat['duenoTel'], 'value' => $v('phone_owner'), 'label' => '¿De quién es este número?', 'wClass' => 'w4'])
            <div class="campo w4">
                <label class="form-label">Correo alterno (opcional)</label>
                <input type="text" class="form-control-modern" value="{{ $v('email') }}" disabled placeholder="Usa el correo de Identificación">
            </div>

            <div class="campo w12">
                <label class="form-label" style="cursor:pointer;">
                    <input type="checkbox" x-model="showAltContact" style="width:auto;">
                    Agregar un contacto alterno
                </label>
            </div>
            <template x-if="showAltContact">
                <div class="campo w12">
                    <div class="rejilla">
                        <div class="campo w4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="alt_contact_name" id="altNombre" class="form-control-modern" x-model="altContactName">
                        </div>
                        @include('livewire.partials.catalog-select', ['name' => 'alt_contact_relationship', 'id' => 'altParentesco', 'options' => $cat['parentesco'], 'value' => $v('alt_contact_relationship'), 'label' => 'Parentesco', 'wClass' => 'w4'])
                        <div class="campo w4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="alt_contact_phone" id="altTelefono" class="form-control-modern" value="{{ $v('alt_contact_phone') }}">
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══ FUENTE DE REFERENCIA ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-bullhorn"></i>
            <h3>Fuente de Referencia</h3>
        </div>
        <div class="rejilla">
            <div class="campo w4">
                <label class="form-label">¿Cómo se enteró de nuestros servicios?</label>
                <select name="referral_source" class="form-control-modern" x-model="referralSource">
                    <option value="">Selecciona una opción</option>
                    @foreach($cat['fuente'] as $option)
                        <option value="{{ $option }}" {{ $v('referral_source') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo w4" x-show="referralSource === 'Otro'" x-cloak>
                <label class="form-label">¿Cuál?</label>
                <input type="text" name="referral_source_other" class="form-control-modern" value="{{ $v('referral_source_other') }}">
            </div>
            <div class="campo w4">
                <label class="form-label">Médico remitente</label>
                <input type="text" name="referring_doctor" class="form-control-modern" value="{{ $v('referring_doctor') }}">
            </div>
        </div>
    </div>

    {{-- ═══ CONTACTO DE EMERGENCIA ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-first-aid"></i>
            <h3>Contacto de Emergencia</h3>
        </div>
        <div class="rejilla">
            <div class="campo w4">
                <label class="form-label">Nombre</label>
                <input type="text" name="emergency_contact_name" id="emergenciaNombre" class="form-control-modern" value="{{ $v('emergency_contact_name') }}" placeholder="En caso de emergencia en consultorio">
            </div>
            @include('livewire.partials.catalog-select', ['name' => 'emergency_contact_relationship', 'id' => 'emergenciaParentesco', 'options' => $cat['parentesco'], 'value' => $v('emergency_contact_relationship'), 'label' => 'Parentesco', 'wClass' => 'w4'])
            <div class="campo w4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="emergency_contact_phone" id="emergenciaTelefono" class="form-control-modern" value="{{ $v('emergency_contact_phone') }}">
            </div>
            <div class="campo w12">
                <button type="button" class="btn-copiar" :disabled="!showAltContact || !altContactName"
                    @click="copiarPersona('alt', 'emergenciaNombre', 'emergenciaParentesco', 'emergenciaTelefono')">
                    <i class="fas fa-copy"></i> Usar el mismo contacto alterno
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ REPRESENTANTE LEGAL ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-users"></i>
            <h3>Representante Legal</h3>
        </div>
        <div class="banda" x-show="esMenor" x-cloak style="margin-bottom:16px;">
            <i class="fas fa-exclamation-triangle"></i>
            El paciente es menor de edad: se recomienda registrar un representante legal.
        </div>
        <div class="rejilla" x-init="$watch('esMenor', (value) => { if (value) guardianRequired = true; })">
            <div class="campo w12">
                <label class="form-label" style="cursor:pointer;">
                    <input type="checkbox" name="guardian_required" value="1" x-model="guardianRequired" style="width:auto;">
                    Este paciente requiere representante legal
                </label>
            </div>
            <template x-if="guardianRequired">
                <div class="campo w12">
                    <div class="rejilla">
                        <div class="campo w12">
                            <button type="button" class="btn-copiar" :disabled="!showAltContact || !altContactName"
                                @click="copiarPersona('alt', 'guardianNombre', null, null)">
                                <i class="fas fa-copy"></i> Usar el mismo contacto alterno
                            </button>
                        </div>
                        <div class="campo w4">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="guardian_name" id="guardianNombre" class="form-control-modern" value="{{ $v('guardian_name') }}">
                        </div>
                        <div class="campo w4">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="guardian_birth_date" class="form-control-modern" value="{{ $v('guardian_birth_date') ? \Carbon\Carbon::parse($v('guardian_birth_date'))->format('Y-m-d') : '' }}">
                        </div>
                        <div class="campo w4">
                            <label class="form-label">Sexo</label>
                            <select name="guardian_gender" class="form-control-modern">
                                <option value="">Selecciona una opción</option>
                                @foreach($cat['parentesco'] as $ignore)
                                @endforeach
                                <option value="femenino" {{ $v('guardian_gender') === 'femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="masculino" {{ $v('guardian_gender') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="otro" {{ $v('guardian_gender') === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══ SECCIÓN PEDIÁTRICA ═══ --}}
    @php
        $ped = old('pediatric_history', $p?->pediatric_history ?? []);
        $hasPediatricData = !empty($ped['is_pediatric']) || !empty($ped['gestational_age']) ||
                             !empty($ped['milestone_head_control']) || !empty($ped['milestone_sitting']) ||
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
                    <input type="text" name="pediatric_history[gestational_age]" class="form-control-modern" value="{{ $ped['gestational_age'] ?? '' }}" placeholder="Ej. 38 semanas">
                </div>
                <div class="form-group">
                    <label class="form-label">Peso al Nacer (g)</label>
                    <input type="text" name="pediatric_history[birth_weight]" class="form-control-modern" value="{{ $ped['birth_weight'] ?? '' }}" placeholder="Ej. 3100g">
                </div>
                <div class="form-group">
                    <label class="form-label">Talla al Nacer (cm)</label>
                    <input type="text" name="pediatric_history[birth_height]" class="form-control-modern" value="{{ $ped['birth_height'] ?? '' }}" placeholder="Ej. 50cm">
                </div>
                <div class="form-group">
                    <label class="form-label">Puntaje Apgar</label>
                    <input type="text" name="pediatric_history[apgar]" class="form-control-modern" value="{{ $ped['apgar'] ?? '' }}" placeholder="Ej. 9/10">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Parto</label>
                    <select name="pediatric_history[delivery_type]" class="form-control-modern">
                        <option value="">Seleccionar...</option>
                        <option value="vaginal" {{ ($ped['delivery_type'] ?? '') === 'vaginal' ? 'selected' : '' }}>Vaginal / Eutócico</option>
                        <option value="cesarea" {{ ($ped['delivery_type'] ?? '') === 'cesarea' ? 'selected' : '' }}>Cesárea</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Hospitalización Neonatal (días/motivo)</label>
                    <input type="text" name="pediatric_history[neonatal_hospitalization]" class="form-control-modern" value="{{ $ped['neonatal_hospitalization'] ?? '' }}" placeholder="Ej. Ninguna / 3 días por ictericia">
                </div>
                <div class="form-group" style="grid-column: span 3;">
                    <label class="form-label">Complicaciones en Embarazo/Parto</label>
                    <textarea name="pediatric_history[complications]" class="form-control-modern" rows="2" spellcheck="true" placeholder="Describa si hubo preeclampsia, cordón en cuello, etc.">{{ $ped['complications'] ?? '' }}</textarea>
                </div>
            </div>

            <h4 style="margin:0 0 12px; font-size:14px; font-weight:600; color:var(--text-secondary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">Hitos del Desarrollo Psicomotor (Edad aproximada)</h4>
            <div class="grid grid-3 mb-24">
                <div class="form-group">
                    <label class="form-label">Control Cefálico</label>
                    <input type="text" name="pediatric_history[milestone_head_control]" class="form-control-modern" value="{{ $ped['milestone_head_control'] ?? '' }}" placeholder="Ej. 3 meses">
                </div>
                <div class="form-group">
                    <label class="form-label">Sedestación (Sentado)</label>
                    <input type="text" name="pediatric_history[milestone_sitting]" class="form-control-modern" value="{{ $ped['milestone_sitting'] ?? '' }}" placeholder="Ej. 6 meses">
                </div>
                <div class="form-group">
                    <label class="form-label">Gateo</label>
                    <input type="text" name="pediatric_history[milestone_crawling]" class="form-control-modern" value="{{ $ped['milestone_crawling'] ?? '' }}" placeholder="Ej. 8 meses">
                </div>
                <div class="form-group">
                    <label class="form-label">Bipedestación (Parado)</label>
                    <input type="text" name="pediatric_history[milestone_standing]" class="form-control-modern" value="{{ $ped['milestone_standing'] ?? '' }}" placeholder="Ej. 10 meses">
                </div>
                <div class="form-group">
                    <label class="form-label">Marcha (Caminar)</label>
                    <input type="text" name="pediatric_history[milestone_walking]" class="form-control-modern" value="{{ $ped['milestone_walking'] ?? '' }}" placeholder="Ej. 12 meses">
                </div>
                <div class="form-group">
                    <label class="form-label">Desarrollo del Lenguaje</label>
                    <input type="text" name="pediatric_history[language_development]" class="form-control-modern" value="{{ $ped['language_development'] ?? '' }}" placeholder="Ej. Monosílabas a los 10m">
                </div>
            </div>

            <h4 style="margin:0 0 12px; font-size:14px; font-weight:600; color:var(--text-secondary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:6px;">Otros Antecedentes</h4>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Esquema de Vacunación</label>
                    <select name="pediatric_history[vaccinations]" class="form-control-modern">
                        <option value="">Seleccionar...</option>
                        <option value="completo" {{ ($ped['vaccinations'] ?? '') === 'completo' ? 'selected' : '' }}>Completo para la edad</option>
                        <option value="incompleto" {{ ($ped['vaccinations'] ?? '') === 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Reflejos / Tono Muscular</label>
                    <input type="text" name="pediatric_history[reflexes]" class="form-control-modern" value="{{ $ped['reflexes'] ?? '' }}" placeholder="Ej. Reflejos primitivos integrados">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Observaciones Adicionales del Desarrollo</label>
                    <textarea name="pediatric_history[other_development]" class="form-control-modern" rows="2" spellcheck="true" placeholder="Otras observaciones específicas...">{{ $ped['other_development'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ENTORNO Y SITUACIÓN SOCIAL ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-home"></i>
            <h3>Entorno y Situación Social</h3>
        </div>
        <div class="rejilla">
            <div class="campo w4">
                <label class="form-label">Lugar de nacimiento</label>
                <input type="text" name="birth_place" class="form-control-modern" value="{{ $v('birth_place') }}">
            </div>
            <div class="campo w4">
                <label class="form-label">Calle y número</label>
                <input type="text" name="street_address" class="form-control-modern" value="{{ $v('street_address') }}">
            </div>
            <div class="campo w4">
                <label class="form-label">Colonia</label>
                <input type="text" name="neighborhood" class="form-control-modern" value="{{ $v('neighborhood') }}">
            </div>
            <div class="campo w4">
                <label class="form-label">Municipio / Ciudad</label>
                <input type="text" name="municipality" class="form-control-modern" value="{{ $v('municipality') }}">
            </div>
            @include('livewire.partials.catalog-select', ['name' => 'zone', 'options' => $cat['zona'], 'value' => $v('zone'), 'label' => 'Zona', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'housing_type', 'options' => $cat['casa'], 'value' => $v('housing_type'), 'label' => 'Casa habitación', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'transportation', 'options' => $cat['transporte'], 'value' => $v('transportation'), 'label' => 'Transporte', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'education_level', 'options' => $cat['escolaridad'], 'value' => $v('education_level'), 'label' => 'Escolaridad', 'wClass' => 'w4'])
            <div class="campo w4">
                <label class="form-label">Ocupación</label>
                <input type="text" name="occupation" class="form-control-modern" value="{{ $v('occupation') }}">
            </div>
            @include('livewire.partials.catalog-select', ['name' => 'economic_dependency', 'options' => $cat['dependencia'], 'value' => $v('economic_dependency'), 'label' => 'Dependencia económica', 'wClass' => 'w4'])
            <div class="campo w12">
                <label class="form-label">Cuidador Primario</label>
                <input type="text" name="primary_caregiver" class="form-control-modern" value="{{ $v('primary_caregiver') }}" placeholder="En caso de incapacidad del paciente">
            </div>
            <div class="campo w12">
                <label class="form-label">Barreras arquitectónicas</label>
                <div class="casillas">
                    @foreach($cat['barreras'] as $barrera)
                        <label class="casilla">
                            <input type="checkbox" name="architectural_barriers[]" value="{{ $barrera }}" {{ in_array($barrera, $archBarriers) ? 'checked' : '' }}>
                            {{ $barrera }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="campo w12">
                <label class="form-label">Animales domésticos</label>
                <div class="casillas">
                    @foreach($cat['animales'] as $animal)
                        <label class="casilla">
                            <input type="checkbox" name="pets[]" value="{{ $animal }}" {{ in_array($animal, $petsChecked) ? 'checked' : '' }}>
                            {{ $animal }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ANTECEDENTES PERSONALES NO PATOLÓGICOS ═══ --}}
    <div class="glass-card mb-24" x-data="{ stress: stressLevel }">
        <div class="section-header">
            <i class="fas fa-leaf"></i>
            <h3>Antecedentes Personales No Patológicos</h3>
        </div>
        <div class="rejilla">
            @include('livewire.partials.catalog-select', ['name' => 'laterality', 'options' => $cat['lateralidad'], 'value' => $v('laterality'), 'label' => 'Lateralidad', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'marital_status', 'options' => $cat['civil'], 'value' => $v('marital_status'), 'label' => 'Estado civil', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'physical_build', 'options' => $cat['complexion'], 'value' => $v('physical_build'), 'label' => 'Complexión física', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'work_demand', 'options' => $cat['laboral'], 'value' => $v('work_demand'), 'label' => 'Exigencia laboral', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'diet', 'options' => $cat['dieta'], 'value' => $v('diet'), 'label' => 'Dieta', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'adl_independence', 'options' => $cat['avd'], 'value' => $v('adl_independence'), 'label' => 'Actividades de la vida diaria (AVD)', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'perceived_health', 'options' => $cat['salud'], 'value' => $v('perceived_health'), 'label' => 'Estado de salud percibido', 'wClass' => 'w4'])
            @include('livewire.partials.catalog-select', ['name' => 'sleep_habits', 'options' => $cat['sueno'], 'value' => $v('sleep_habits'), 'label' => 'Hábitos de sueño', 'wClass' => 'w4'])
            <div class="campo w4">
                <label class="form-label">Nivel de estrés (0 a 10)</label>
                <div class="escala">
                    <input type="range" name="stress_level" min="0" max="10" step="1" x-model="stress">
                    <strong x-text="stress"></strong>
                </div>
            </div>
            <div class="campo w12">
                <label class="form-label">Otros</label>
                <textarea name="non_pathological_notes" class="form-control-modern" rows="2">{{ $v('non_pathological_notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ═══ ANTECEDENTES (FAMILIARES Y PATOLÓGICOS) ═══ --}}
    <div class="glass-card mb-24" x-data="{
        matrix: @js($mhm),
        denyFam: {{ $v('medical_history_deny_family', false) ? 'true' : 'false' }},
        denyPac: {{ $v('medical_history_deny_patient', false) ? 'true' : 'false' }},
        antecedentesCat: @js($antecedentesCat),
        onDenyFam() { if (this.denyFam) { Object.keys(this.matrix).forEach(k => { this.matrix[k].fam = false; }); } },
        onDenyPac() { if (this.denyPac) { Object.keys(this.matrix).forEach(k => { this.matrix[k].pac = false; }); } },
    }">
        <div class="section-header">
            <i class="fas fa-dna"></i>
            <h3>Antecedentes Heredofamiliares y Personales Patológicos</h3>
        </div>
        <div class="matriz">
            <div class="matriz-cabeza">
                <div>Condición</div>
                <div style="text-align:center;">Familiar</div>
                <div style="text-align:center;">Paciente</div>
            </div>
            <div class="matriz-fila">
                <div><strong>Niega todo</strong></div>
                <label><input type="checkbox" name="medical_history_deny_family" value="1" x-model="denyFam" @change="onDenyFam()"></label>
                <label><input type="checkbox" name="medical_history_deny_patient" value="1" x-model="denyPac" @change="onDenyPac()"></label>
            </div>
            <template x-for="(cond, key) in antecedentesCat" :key="key">
                <div class="matriz-fila">
                    <div x-text="cond.n"></div>
                    <label>
                        <input type="checkbox" :name="`medical_history_matrix[${key}][fam]`" value="1" x-model="matrix[key].fam" :disabled="denyFam">
                    </label>
                    <label>
                        <input type="checkbox" :name="`medical_history_matrix[${key}][pac]`" value="1" x-model="matrix[key].pac" :disabled="denyPac">
                    </label>
                    <template x-if="matrix[key].fam || matrix[key].pac">
                        <div class="matriz-detalle">
                            <div x-show="matrix[key].fam" x-cloak>
                                <span class="etiqueta-quien">Familiar</span>
                                <textarea :name="`medical_history_matrix[${key}][fam_detalle]`" class="form-control-modern" rows="1" x-model="matrix[key].fam_detalle" placeholder="Quién la tiene y detalles"></textarea>
                                <div class="fichas">
                                    <template x-for="chip in cond.f" :key="chip">
                                        <button type="button" class="ficha" :class="chipActiva(matrix[key], 'fam_detalle', chip) ? 'activa' : ''"
                                            @click="toggleChipIn(matrix[key], 'fam_detalle', chip)" x-text="chip"></button>
                                    </template>
                                </div>
                            </div>
                            <div x-show="matrix[key].pac" x-cloak>
                                <span class="etiqueta-quien">Paciente</span>
                                <textarea :name="`medical_history_matrix[${key}][pac_detalle]`" class="form-control-modern" rows="1" x-model="matrix[key].pac_detalle" placeholder="Tipo, desde cuándo, tratamiento"></textarea>
                                <div class="fichas">
                                    <template x-for="chip in cond.f" :key="chip">
                                        <button type="button" class="ficha" :class="chipActiva(matrix[key], 'pac_detalle', chip) ? 'activa' : ''"
                                            @click="toggleChipIn(matrix[key], 'pac_detalle', chip)" x-text="chip"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
        <div class="campo w12" style="margin-top:16px;">
            <label class="form-label">Otros antecedentes</label>
            <textarea name="medical_history_notes" class="form-control-modern" rows="2">{{ $v('medical_history_notes') }}</textarea>
        </div>
    </div>

    {{-- ═══ EVENTOS Y MEDICACIÓN ═══ --}}
    <div class="glass-card mb-24" x-data="{
        hosp: @js(count($hosp) ? $hosp : []),
        cirug: @js(count($cirug) ? $cirug : []),
        traum: @js(count($traum) ? $traum : []),
        medic: @js(count($medic) ? $medic : []),
    }">
        <div class="section-header">
            <i class="fas fa-notes-medical"></i>
            <h3>Eventos y Medicación</h3>
        </div>
        <div class="rejilla">
            <div class="campo w12">
                <label class="form-label">Hábitos tóxicos</label>
                <div class="casillas">
                    @foreach($cat['toxicos'] as $habito)
                        <label class="casilla">
                            <input type="checkbox" name="toxic_habits[]" value="{{ $habito }}" {{ in_array($habito, $toxicHabits) ? 'checked' : '' }}>
                            {{ $habito }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="campo w12">
                <label class="form-label">Alergias</label>
                <div class="casillas">
                    @foreach($cat['alergias'] as $alergia)
                        <label class="casilla">
                            <input type="checkbox" name="allergies[]" value="{{ $alergia }}" {{ in_array($alergia, $allergiesChecked) ? 'checked' : '' }}>
                            {{ $alergia }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="campo w12">
                <label class="form-label">Detalle de alergias</label>
                <textarea name="allergies_detail" class="form-control-modern" rows="2">{{ $v('allergies_detail') }}</textarea>
            </div>

            @foreach(['hosp' => ['col' => 'hospitalizations', 'meta' => $listasRepetiblesCat['hosp']], 'cirug' => ['col' => 'surgeries', 'meta' => $listasRepetiblesCat['cirug']], 'traum' => ['col' => 'traumas', 'meta' => $listasRepetiblesCat['traum']]] as $listKey => $info)
                <div class="campo w12" x-data>
                    <label class="form-label">{{ $info['meta']['n'] }}</label>
                    <div class="lista">
                        <template x-for="(row, idx) in {{ $listKey }}" :key="idx">
                            <div class="renglon">
                                <button type="button" class="quitar" @click="{{ $listKey }}.splice(idx, 1)">&times;</button>
                                <input type="text" :name="`{{ $info['col'] }}[${idx}][detalle]`" class="form-control-modern" x-model="row.detalle" placeholder="{{ $info['meta']['col'] }}">
                                <div class="fichas">
                                    @foreach($info['meta']['f'] as $chip)
                                        <button type="button" class="ficha" :class="chipActiva(row, 'detalle', '{{ $chip }}') ? 'activa' : ''" @click="toggleChipIn(row, 'detalle', '{{ $chip }}')">{{ $chip }}</button>
                                    @endforeach
                                </div>
                                <div class="rejilla">
                                    <div class="campo w6">
                                        <select :name="`{{ $info['col'] }}[${idx}][mes]`" class="form-control-modern" x-model="row.mes">
                                            <option value="">Mes (opcional)</option>
                                            @foreach($meses as $mes)
                                                <option value="{{ $mes }}">{{ $mes }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="campo w6">
                                        <select :name="`{{ $info['col'] }}[${idx}][cuando]`" class="form-control-modern" x-model="row.cuando">
                                            @foreach($opcionesCuando as $valOpt => $labelOpt)
                                                <option value="{{ $valOpt }}">{{ $labelOpt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" class="btn-modern btn-secondary btn-sm agregar" @click="{{ $listKey }}.push({detalle:'', mes:'', cuando:''})">
                            <i class="fas fa-plus"></i> Agregar {{ Str::lower($info['meta']['n']) }}
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="campo w12">
                <label class="form-label">{{ $listasRepetiblesCat['medic']['n'] }}</label>
                <div class="lista">
                    <template x-for="(row, idx) in medic" :key="idx">
                        <div class="renglon">
                            <button type="button" class="quitar" @click="medic.splice(idx, 1)">&times;</button>
                            <input type="text" :name="`current_medications[${idx}][detalle]`" class="form-control-modern" x-model="row.detalle" placeholder="Medicamento">
                            <div class="fichas">
                                @foreach($listasRepetiblesCat['medic']['f'] as $chip)
                                    <button type="button" class="ficha" :class="chipActiva(row, 'detalle', '{{ $chip }}') ? 'activa' : ''" @click="toggleChipIn(row, 'detalle', '{{ $chip }}')">{{ $chip }}</button>
                                @endforeach
                            </div>
                            <input type="text" :name="`current_medications[${idx}][dosis]`" class="form-control-modern" x-model="row.dosis" placeholder="Dosis e indicación">
                        </div>
                    </template>
                    <button type="button" class="btn-modern btn-secondary btn-sm agregar" @click="medic.push({detalle:'', dosis:''})">
                        <i class="fas fa-plus"></i> Agregar medicamento
                    </button>
                </div>
            </div>

            <div class="campo w12">
                <label class="form-label">Otros eventos</label>
                <textarea name="events_notes" class="form-control-modern" rows="2">{{ $v('events_notes') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ═══ INTERROGATORIO POR SÍNTOMAS ═══ --}}
    <div class="glass-card mb-24" x-data="{
        sintomas: @js($sym),
        interrogatorioCat: @js($interrogatorioCat),
        get banderas() {
            const activas = [];
            Object.entries(this.interrogatorioCat).forEach(([key, info]) => {
                if (info.bandera && this.sintomas[key] && this.sintomas[key].presente) activas.push(info.bandera);
            });
            return activas;
        }
    }">
        <div class="section-header">
            <i class="fas fa-stethoscope"></i>
            <h3>Interrogatorio por Aparatos y Sistemas</h3>
        </div>
        <div class="banda banda-alerta" x-show="banderas.length" x-cloak>
            <i class="fas fa-flag"></i>
            <span>Banderas clínicas: <span x-text="banderas.join(' · ')"></span></span>
        </div>
        <div class="sintomas">
            <template x-for="(info, key) in interrogatorioCat" :key="key">
                <div class="sintoma">
                    <div class="sintoma-cabeza">
                        <span class="sintoma-nombre" x-text="info.n"></span>
                        <span class="sino">
                            <label :class="sintomas[key].presente === true ? '' : ''">
                                <input type="radio" :name="`symptoms_review[${key}][presente]`" value="1" x-model.boolean="sintomas[key].presente">
                                Sí
                            </label>
                            <label>
                                <input type="radio" :name="`symptoms_review[${key}][presente]`" value="0" :checked="sintomas[key].presente === false" @change="sintomas[key].presente = false">
                                No
                            </label>
                        </span>
                    </div>
                    <template x-if="sintomas[key].presente">
                        <div class="sintoma-detalle">
                            <textarea :name="`symptoms_review[${key}][detalle]`" class="form-control-modern" rows="1" x-model="sintomas[key].detalle" placeholder="Detalle"></textarea>
                            <div class="fichas" x-show="info.f.length" x-cloak>
                                <template x-for="chip in info.f" :key="chip">
                                    <button type="button" class="ficha" :class="chipActiva(sintomas[key], 'detalle', chip) ? 'activa' : ''" @click="toggleChipIn(sintomas[key], 'detalle', chip)" x-text="chip"></button>
                                </template>
                            </div>
                            <div class="sintoma-bandera" x-show="info.bandera" x-cloak>
                                <i class="fas fa-flag"></i> <span x-text="info.bandera"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══ OBSERVACIONES ═══ --}}
    <div class="glass-card mb-24">
        <div class="section-header">
            <i class="fas fa-clipboard"></i>
            <h3>Observaciones del Fisioterapeuta</h3>
        </div>
        <div class="rejilla">
            <div class="campo w12">
                <textarea name="observations" class="form-control-modern" rows="3">{{ $v('observations') }}</textarea>
            </div>
        </div>
    </div>
</div>

@php
    $regionesCat = \App\Support\ExpedienteCatalogo::regiones();
    $antecedentesCat = \App\Support\ExpedienteCatalogo::antecedentes();
    $interrogatorioCat = \App\Support\ExpedienteCatalogo::interrogatorio();

    $matrix = array_replace_recursive(\App\Models\Patient::medicalHistoryMatrixDefaults(), $patient->medical_history_matrix ?? []);
    $symptoms = array_replace_recursive(\App\Models\Patient::symptomsReviewDefaults(), $patient->symptoms_review ?? []);
    $bodyRegions = $patient->body_regions ?? [];
    $archBarriers = $patient->architectural_barriers ?? [];
    $pets = $patient->pets ?? [];
    $toxicHabits = $patient->toxic_habits ?? [];
    $allergiesList = $patient->allergies ?? [];

    $val = function ($value) {
        return empty($value) ? '<span style="color:var(--text-muted);">&mdash;</span>' : e($value);
    };
    $tags = function (array $values) use ($val) {
        return empty($values) ? $val(null) : e(implode(', ', $values));
    };
    $listado = function (?array $rows, string $labelCampo) use ($val) {
        if (empty($rows)) return $val(null);
        return collect($rows)->map(function ($row) use ($labelCampo) {
            $detalle = e($row['detalle'] ?? '');
            $extra = trim(($row['mes'] ?? '') . ' ' . (isset($row['cuando']) && !in_array($row['cuando'], ['', null], true) ? '' : ''));
            $dosis = !empty($row['dosis']) ? ' — ' . e($row['dosis']) : '';
            return "<li>{$detalle}{$dosis}</li>";
        })->implode('');
    };
@endphp

<div class="glass-card mb-24">
    <div class="flex-between" style="margin-bottom: 24px;">
        <h2 style="margin:0;font-size:20px;font-weight:700;"><i class="fas fa-file-medical" style="color:var(--text-muted); margin-right:8px;"></i> Expediente Médico</h2>
        <button type="button" class="btn-modern btn-primary btn-sm" onclick="document.getElementById('medical-record-modal').style.display='flex'; document.body.style.overflow='hidden';">
            <i class="fas fa-pen"></i> Editar Expediente
        </button>
    </div>

    {{-- Identificación --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Identificación</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nombre:</strong> {!! $val($patient->name) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Apellidos:</strong> {!! $val($patient->last_name) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Correo Electrónico:</strong> {!! $val($patient->email) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Fecha de Nacimiento:</strong> {!! $val($patient->birth_date?->format('d/m/Y')) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Género:</strong> {!! $val($patient->gender_label) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Edad:</strong> {!! $val($patient->manual_age ?? $patient->age) !!}</div>
            @if(auth()->user()->admin)
                <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Fisioterapeuta Asignado:</strong> {!! $val($patient->user->name ?? null) !!}</div>
                <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Fecha de Ingreso:</strong> {!! $val($patient->created_at?->format('d/m/Y')) !!}</div>
            @endif
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Región corporal:</strong> {!! $tags(collect($bodyRegions)->map(fn($k) => $regionesCat[$k]['n'] ?? $k)->all()) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Padecimiento / Diagnóstico principal:</strong> {!! $val($patient->diagnosis) !!}</div>
        </div>
    </div>

    {{-- Contacto --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Contacto</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Teléfono:</strong> {!! $val($patient->phone) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">¿De quién es este número?:</strong> {!! $val($patient->phone_owner) !!}</div>
            <div></div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Contacto alterno:</strong> {!! $val($patient->alt_contact_name) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Parentesco:</strong> {!! $val($patient->alt_contact_relationship) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Teléfono alterno:</strong> {!! $val($patient->alt_contact_phone) !!}</div>
        </div>
    </div>

    {{-- Fuente de Referencia y Contacto de Emergencia --}}
    <div class="grid grid-2" style="margin-bottom: 32px; gap: 24px;">
        <div>
            <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Fuente de Referencia</h3>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">¿Cómo se enteró?:</strong> {!! $val($patient->referral_source === 'Otro' ? $patient->referral_source_other : $patient->referral_source) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Médico Remitente:</strong> {!! $val($patient->referring_doctor) !!}</div>
        </div>
        <div>
            <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Contacto de Emergencia</h3>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nombre:</strong> {!! $val($patient->emergency_contact_name) !!}</div>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Teléfono:</strong> {!! $val($patient->emergency_contact_phone) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Parentesco:</strong> {!! $val($patient->emergency_contact_relationship) !!}</div>
        </div>
    </div>

    {{-- Representante Legal --}}
    @if($patient->guardian_required || $patient->guardian_name)
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Representante Legal</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nombre:</strong> {!! $val($patient->guardian_name) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Fecha de Nacimiento:</strong> {!! $val($patient->guardian_birth_date?->format('d/m/Y')) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Sexo:</strong> {!! $val($patient->guardian_gender) !!}</div>
        </div>
    </div>
    @endif

    {{-- Sección Pediátrica --}}
    @php
        $ped = $patient->pediatric_history ?? [];
        $hasPediatricData = !empty($ped['is_pediatric']) || !empty($ped['gestational_age']) ||
                             !empty($ped['milestone_head_control']) || !empty($ped['milestone_sitting']) ||
                             !empty($ped['milestone_walking']);
    @endphp
    @if($hasPediatricData)
    <div style="margin-bottom: 32px; border: 1px dashed rgba(20,184,166,0.3); padding: 16px; border-radius: 8px; background: rgba(20,184,166,0.02);">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--accent-primary); border-bottom:1px solid rgba(20,184,166,0.2); padding-bottom:8px;">Antecedentes Pediátricos y de Desarrollo</h3>

        <h4 style="font-size:13px; font-weight:600; margin-bottom: 8px; color:var(--text-secondary);">Antecedentes Perinatales</h4>
        <div class="grid grid-3 mb-16">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Edad Gestacional:</strong> {!! $val($ped['gestational_age'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Peso al Nacer:</strong> {!! $val($ped['birth_weight'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Talla al Nacer:</strong> {!! $val($ped['birth_height'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Apgar:</strong> {!! $val($ped['apgar'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Tipo de Parto:</strong> {!! $val($ped['delivery_type'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hospitalización Neonatal:</strong> {!! $val($ped['neonatal_hospitalization'] ?? '') !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Complicaciones en Embarazo/Parto:</strong> {!! $val($ped['complications'] ?? '') !!}</div>
        </div>

        <h4 style="font-size:13px; font-weight:600; margin-bottom: 8px; color:var(--text-secondary);">Hitos del Desarrollo Psicomotor</h4>
        <div class="grid grid-3 mb-16">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Control Cefálico:</strong> {!! $val($ped['milestone_head_control'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Sedestación:</strong> {!! $val($ped['milestone_sitting'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Gateo:</strong> {!! $val($ped['milestone_crawling'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Bipedestación:</strong> {!! $val($ped['milestone_standing'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Marcha:</strong> {!! $val($ped['milestone_walking'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Desarrollo del Lenguaje:</strong> {!! $val($ped['language_development'] ?? '') !!}</div>
        </div>

        <h4 style="font-size:13px; font-weight:600; margin-bottom: 8px; color:var(--text-secondary);">Otros</h4>
        <div class="grid grid-2">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Esquema de Vacunación:</strong> {!! $val($ped['vaccinations'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Reflejos / Tono:</strong> {!! $val($ped['reflexes'] ?? '') !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Observaciones Adicionales del Desarrollo:</strong> {!! $val($ped['other_development'] ?? '') !!}</div>
        </div>
    </div>
    @endif

    {{-- Entorno y Situación Social --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Entorno y Situación Social</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Lugar de Nacimiento:</strong> {!! $val($patient->birth_place) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Domicilio:</strong> {!! $val(trim(($patient->street_address ?? '') . ' ' . ($patient->neighborhood ?? ''))) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Municipio / Zona:</strong> {!! $val(trim(($patient->municipality ?? '') . ($patient->zone ? ' (' . $patient->zone . ')' : ''))) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Casa Habitación:</strong> {!! $val($patient->housing_type) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Transporte:</strong> {!! $val($patient->transportation) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Escolaridad:</strong> {!! $val($patient->education_level) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Ocupación:</strong> {!! $val($patient->occupation) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Dependencia Económica:</strong> {!! $val($patient->economic_dependency) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cuidador Primario:</strong> {!! $val($patient->primary_caregiver) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Barreras Arquitectónicas:</strong> {!! $tags($archBarriers) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Animales Domésticos:</strong> {!! $tags($pets) !!}</div>
        </div>
    </div>

    {{-- Antecedentes Personales No Patológicos --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Antecedentes Personales No Patológicos</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Lateralidad:</strong> {!! $val($patient->laterality) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Estado Civil:</strong> {!! $val($patient->marital_status) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Complexión Física:</strong> {!! $val($patient->physical_build) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Exigencia Laboral:</strong> {!! $val($patient->work_demand) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Dieta:</strong> {!! $val($patient->diet) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">AVD:</strong> {!! $val($patient->adl_independence) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Estado de Salud:</strong> {!! $val($patient->perceived_health) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hábitos de Sueño:</strong> {!! $val($patient->sleep_habits) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nivel de Estrés:</strong> {!! $val($patient->stress_level !== null ? $patient->stress_level . '/10' : null) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros:</strong> {!! $val($patient->non_pathological_notes) !!}</div>
        </div>
    </div>

    {{-- Antecedentes Heredofamiliares y Personales Patológicos --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Antecedentes Heredofamiliares y Personales Patológicos</h3>
        @if($patient->medical_history_deny_family && $patient->medical_history_deny_patient)
            <p style="color:var(--text-muted); font-size:13px;">Niega antecedentes familiares y personales.</p>
        @else
            <div class="grid grid-2">
                @foreach($antecedentesCat as $key => $cond)
                    @php $row = $matrix[$key] ?? []; @endphp
                    @if(!empty($row['fam']) || !empty($row['pac']))
                        <div>
                            <strong style="color:var(--text-secondary);font-size:12px;display:block;">{{ $cond['n'] }}:</strong>
                            @if(!empty($row['fam']))<span>Familiar{{ !empty($row['fam_detalle']) ? ' ('.e($row['fam_detalle']).')' : '' }}</span>@endif
                            @if(!empty($row['fam']) && !empty($row['pac']))<span> · </span>@endif
                            @if(!empty($row['pac']))<span>Paciente{{ !empty($row['pac_detalle']) ? ' ('.e($row['pac_detalle']).')' : '' }}</span>@endif
                        </div>
                    @endif
                @endforeach
            </div>
            @if(!empty($patient->medical_history_notes))
                <div style="margin-top:12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros antecedentes:</strong> {!! $val($patient->medical_history_notes) !!}</div>
            @endif
        @endif
    </div>

    {{-- Eventos y Medicación --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Eventos y Medicación</h3>
        <div class="grid grid-2">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hábitos Tóxicos:</strong> {!! $tags($toxicHabits) !!}</div>
            @php
                $alergiasTexto = trim(implode(' — ', array_filter([
                    empty($allergiesList) ? '' : implode(', ', $allergiesList),
                    $patient->allergies_detail ?? '',
                ])));
            @endphp
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Alergias:</strong> {!! $val($alergiasTexto) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hospitalizaciones:</strong> <ul style="margin:4px 0 0; padding-left:18px;">{!! $listado($patient->hospitalizations, 'detalle') !!}</ul>{!! empty($patient->hospitalizations) ? $val(null) : '' !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cirugías:</strong> <ul style="margin:4px 0 0; padding-left:18px;">{!! $listado($patient->surgeries, 'detalle') !!}</ul>{!! empty($patient->surgeries) ? $val(null) : '' !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Traumatismos:</strong> <ul style="margin:4px 0 0; padding-left:18px;">{!! $listado($patient->traumas, 'detalle') !!}</ul>{!! empty($patient->traumas) ? $val(null) : '' !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Medicamentos Actuales:</strong> <ul style="margin:4px 0 0; padding-left:18px;">{!! $listado($patient->current_medications, 'detalle') !!}</ul>{!! empty($patient->current_medications) ? $val(null) : '' !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros eventos:</strong> {!! $val($patient->events_notes) !!}</div>
        </div>
    </div>

    {{-- Interrogatorio por Aparatos y Sistemas --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Interrogatorio por Aparatos y Sistemas</h3>
        @php $algunoPresente = collect($symptoms)->contains(fn($row) => !empty($row['presente'])); @endphp
        @if(!$algunoPresente)
            <p style="color:var(--text-muted); font-size:13px;">Sin síntomas referidos.</p>
        @else
            <div class="grid grid-2">
                @foreach($interrogatorioCat as $key => $info)
                    @php $row = $symptoms[$key] ?? []; @endphp
                    @if(!empty($row['presente']))
                        <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">{{ $info['n'] }}:</strong> {!! $val($row['detalle'] ?? '') !!}</div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Observaciones --}}
    <div>
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Observaciones del Fisioterapeuta</h3>
        <p style="white-space:pre-wrap; font-size:14px; line-height:1.6; color:var(--text-primary);">{{ $patient->observations ?? 'Ninguna observación registrada.' }}</p>
    </div>
</div>

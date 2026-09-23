@php
    $record = $patient->medicalRecord;
    $fh = array_merge(\App\Models\MedicalRecord::familyHistoryDefaults(), $record->family_history ?? []);
    $npat = array_merge(\App\Models\MedicalRecord::personalNonPathologicalDefaults(), $record->personal_non_pathological ?? []);
    $pat = array_merge(\App\Models\MedicalRecord::personalPathologicalDefaults(), $record->personal_pathological ?? []);
    $sr = array_merge(\App\Models\MedicalRecord::systemsReviewDefaults(), $record->systems_review ?? []);
    
    // Helper para mostrar un valor o un placeholder
    $val = function($value) {
        return empty($value) ? '<span style="color:var(--text-muted);">&mdash;</span>' : e($value);
    };
@endphp

<div class="glass-card mb-24">
    <div class="flex-between" style="margin-bottom: 24px;">
        <h2 style="margin:0;font-size:20px;font-weight:700;"><i class="fas fa-file-medical" style="color:var(--text-muted); margin-right:8px;"></i> Expediente Médico</h2>
        <button type="button" class="btn-modern btn-primary btn-sm" onclick="document.getElementById('medical-record-modal').style.display='flex'; document.body.style.overflow='hidden';">
            <i class="fas fa-pen"></i> Editar Expediente
        </button>
    </div>

    {{-- Datos Personales --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Datos Personales</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Edad:</strong> {!! $val($record->age) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Estado Civil:</strong> {!! $val($record->marital_status) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Domicilio:</strong> {!! $val($record->address) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Lateralidad:</strong> {!! $val($record->laterality) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Escolaridad / Ocupación:</strong> {!! $val($record->occupation) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Dependencia Económica:</strong> {!! $val($record->economic_dependency) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cuidador Primario:</strong> {!! $val($record->primary_caregiver) !!}</div>
        </div>
    </div>

    {{-- Fuente de Referencia y Contacto de Emergencia --}}
    <div class="grid grid-2" style="margin-bottom: 32px; gap: 24px;">
        <div>
            <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Fuente de Referencia</h3>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">¿Cómo se enteró?:</strong> {!! $val($npat['referencia_como_entero'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Médico Remitente:</strong> {!! $val($npat['referencia_medico'] ?? '') !!}</div>
        </div>
        <div>
            <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Contacto de Emergencia</h3>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nombre:</strong> {!! $val($npat['emergencia_nombre'] ?? '') !!}</div>
            <div style="margin-bottom: 12px;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Teléfono:</strong> {!! $val($npat['emergencia_telefono'] ?? '') !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Parentesco:</strong> {!! $val($npat['emergencia_parentesco'] ?? '') !!}</div>
        </div>
    </div>

    {{-- Representante Legal --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Representante Legal</h3>
        <div class="grid grid-2">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nombre:</strong> {!! $val($record->guardian_name) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Fecha Nac. / Edad:</strong> {!! $val($record->guardian_birth_date ? $record->guardian_birth_date->format('d/m/Y') : null) !!} ({{ $record->guardian_age ?? '--' }} años)</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Sexo:</strong> {!! $val($record->guardian_gender) !!}</div>
        </div>
    </div>

    {{-- Sección Pediátrica --}}
    @php
        $ped = $record->pediatric_history ?? [];
        $hasPediatricData = !empty($ped['is_pediatric']) || 
                            !empty($ped['gestational_age']) || 
                            !empty($ped['milestone_head_control']) || 
                            !empty($ped['milestone_sitting']) || 
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

    {{-- Antecedentes Personales No Patológicos --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Antecedentes Personales No Patológicos</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Lugar Nacimiento:</strong> {!! $val($npat['lugar_nacimiento']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Residencia Actual:</strong> {!! $val($npat['residencia_actual']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Casa Habitación:</strong> {!! $val($npat['casa_habitacion']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Transporte:</strong> {!! $val($npat['transporte']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nivel Socioeconómico:</strong> {!! $val($npat['nivel_socioeconomico']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Animales Domésticos:</strong> {!! $val($npat['animales_domesticos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Complexión Física:</strong> {!! $val($npat['complexion_fisica']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Nivel de Estrés:</strong> {!! $val($npat['nivel_estres']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Exigencia Laboral:</strong> {!! $val($npat['exigencia_laboral']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Dieta:</strong> {!! $val($npat['dieta']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">AVD:</strong> {!! $val($npat['avd']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Estado de Salud:</strong> {!! $val($npat['estado_salud']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hábitos de Sueño:</strong> {!! $val($npat['habitos_sueno']) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros:</strong> {!! $val($npat['otros']) !!}</div>
        </div>
    </div>

    {{-- Antecedentes Heredofamiliares --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Antecedentes Heredofamiliares</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Diabetes:</strong> {!! $val($fh['diabetes']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cardíacas:</strong> {!! $val($fh['cardiacas']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Vasculares:</strong> {!! $val($fh['vasculares']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Pulmonares:</strong> {!! $val($fh['pulmonares']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Neoplasias:</strong> {!! $val($fh['neoplasias']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Metabólicos:</strong> {!! $val($fh['metabolicos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Síndromes:</strong> {!! $val($fh['sindromes']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Sistema Nervioso:</strong> {!! $val($fh['sistema_nervioso']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Autoinmunes:</strong> {!! $val($fh['autoinmunes']) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros:</strong> {!! $val($fh['otros']) !!}</div>
        </div>
    </div>

    {{-- Antecedentes Personales Patológicos --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Antecedentes Personales Patológicos</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hospitalización:</strong> {!! $val($pat['hospitalizacion']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cirugías:</strong> {!! $val($pat['cirugias']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hábitos Tóxicos:</strong> {!! $val($pat['habitos_toxicos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Alergias:</strong> {!! $val($pat['alergias']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Traumatismos:</strong> {!! $val($pat['traumatismos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Convulsiones:</strong> {!! $val($pat['convulsiones']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Neoplasias:</strong> {!! $val($pat['neoplasias']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Hipertensión:</strong> {!! $val($pat['hipertension']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Diabetes:</strong> {!! $val($pat['diabetes']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Reumatológicos:</strong> {!! $val($pat['reumatologicos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Psiquiátricos:</strong> {!! $val($pat['psiquiatricos']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Enfermedades Congénitas:</strong> {!! $val($pat['enfermedades_congenitas']) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Medicamentos Actuales:</strong> {!! $val($pat['medicamentos_actuales']) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros:</strong> {!! $val($pat['otros']) !!}</div>
        </div>
    </div>

    {{-- Interrogatorio por Aparatos y Sistemas --}}
    <div style="margin-bottom: 32px;">
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Interrogatorio por Aparatos y Sistemas</h3>
        <div class="grid grid-3">
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Alt. Visuales:</strong> {!! $val($sr['visual']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Alt. Auditivas:</strong> {!! $val($sr['auditivo']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Alt. Sensoriales:</strong> {!! $val($sr['sensorial']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Musculoesquelético:</strong> {!! $val($sr['musculoesqueletico']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Sistema Nervioso:</strong> {!! $val($sr['sistema_nervioso']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Metabólico:</strong> {!! $val($sr['metabolico']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Respiratorio:</strong> {!! $val($sr['respiratorio']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Cardíaco:</strong> {!! $val($sr['cardiaco']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Gastrointestinal:</strong> {!! $val($sr['gastrointestinal']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Genitourinario:</strong> {!! $val($sr['genitourinario']) !!}</div>
            <div><strong style="color:var(--text-secondary);font-size:12px;display:block;">Piel:</strong> {!! $val($sr['piel']) !!}</div>
            <div style="grid-column: 1/-1;"><strong style="color:var(--text-secondary);font-size:12px;display:block;">Otros:</strong> {!! $val($sr['otros']) !!}</div>
        </div>
    </div>

    {{-- Observaciones --}}
    <div>
        <h3 style="font-size:16px; font-weight:600; margin-bottom: 16px; color:var(--text-primary); border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Observaciones Generales</h3>
        <p style="white-space:pre-wrap; font-size:14px; line-height:1.6; color:var(--text-primary);">{{ $record->observations ?? 'Ninguna observación registrada.' }}</p>
    </div>
</div>

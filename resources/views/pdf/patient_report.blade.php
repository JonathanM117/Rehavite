<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente — {{ $patient->full_name }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size:10.5px; color:#1a1a2e; background:#fff; }

        /* HEADER */
        .header-box { background:#0f3460; padding:14px 20px; margin-bottom:16px; }
        .header-box td { vertical-align:middle; }
        .header-logo { width:58px; height:58px; border-radius:8px; object-fit:contain; background:white; padding:3px; }
        .header-title { color:#fff; font-size:19px; font-weight:bold; padding-left:14px; }
        .header-sub { color:rgba(255,255,255,0.8); font-size:10px; padding-left:14px; }
        .header-meta { text-align:right; color:rgba(255,255,255,0.8); font-size:9.5px; white-space:nowrap; }

        /* SECTIONS */
        .section { margin:0 16px 14px 16px; }
        .section-title { background:#0f3460; color:#fff; padding:5px 12px; font-size:10.5px; font-weight:bold; border-left:4px solid #1abc9c; margin-bottom:8px; }
        .sub-title { background:#1a5276; color:#fff; padding:4px 10px; font-size:10px; font-weight:bold; margin-bottom:6px; margin-top:8px; }

        /* GRID */
        .grid-2 { width:100%; border-collapse:collapse; }
        .col { width:50%; vertical-align:top; padding:0 5px; }
        .col:first-child { padding-left:0; }
        .col:last-child { padding-right:0; }

        /* FIELDS */
        .field { margin-bottom:6px; }
        .field-label { font-weight:bold; color:#0f3460; font-size:9px; text-transform:uppercase; letter-spacing:0.3px; display:block; margin-bottom:2px; }
        .field-value { display:block; background:#f0f4f8; padding:4px 8px; border-left:3px solid #1abc9c; font-size:10.5px; min-height:20px; }
        .field-value-empty { color:#aaa; }

        /* BOXES */
        .diag-box { background:#eaf6f6; border:1px solid #a8d9d9; padding:8px 12px; font-size:10.5px; }
        .info-box  { background:#f8f9fa; border:1px solid #dee2e6; padding:8px 12px; font-size:10.5px; margin-bottom:6px; }

        /* CONSULTA */
        .consulta-block { border:1px solid #cdd5e0; margin-bottom:12px; page-break-inside:avoid; }
        .consulta-hdr { background:#0f3460; padding:7px 12px; width:100%; border-collapse:collapse; }
        .ch-title { color:#fff; font-weight:bold; font-size:10.5px; }
        .ch-date  { color:rgba(255,255,255,0.75); font-size:9.5px; text-align:right; width:100px; }
        .ch-status { text-align:right; }
        .badge-open       { background:#d4edda; color:#155724; padding:2px 7px; font-size:9px; font-weight:bold; }
        .badge-in_progress{ background:#d1ecf1; color:#0c5460; padding:2px 7px; font-size:9px; font-weight:bold; }
        .badge-closed     { background:#f8d7da; color:#721c24; padding:2px 7px; font-size:9px; font-weight:bold; }
        .consulta-body { padding:10px 12px; }

        /* NOTES */
        .notes-title { font-size:9.5px; font-weight:bold; color:#0f3460; text-transform:uppercase; letter-spacing:0.3px; margin:8px 0 5px; padding-top:7px; border-top:1px solid #e0e6ed; }
        .note-block { background:#f8fafc; border-left:3px solid #1abc9c; padding:6px 10px; margin-bottom:6px; page-break-inside:avoid; }
        .note-meta  { font-size:9px; color:#555; font-weight:bold; margin-bottom:3px; }
        .note-row   { font-size:10px; margin-bottom:2px; }
        .note-lbl   { font-weight:bold; color:#0f3460; }

        /* JSON GRID */
        .json-grid { width:100%; border-collapse:collapse; font-size:10px; }
        .json-grid td { padding:3px 6px; border-bottom:1px solid #f0f0f0; vertical-align:top; }
        .json-key { color:#0f3460; font-weight:bold; width:40%; }
        .json-val { color:#333; }

        /* VITAL SIGNS */
        .vitals-table { width:100%; border-collapse:collapse; font-size:10px; margin-top:4px; }
        .vitals-table th { background:#2980b9; color:white; padding:3px 6px; text-align:left; font-size:9px; }
        .vitals-table td { padding:3px 6px; border:1px solid #dee2e6; }

        /* FOOTER */
        .footer { text-align:center; font-size:9px; color:#aaa; padding:12px; border-top:1px solid #eee; margin-top:14px; }
        .divider { border:none; border-top:1px solid #e8ecf0; margin:8px 0; }
    </style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="header-box">
    <table width="100%">
        <tr>
            <td width="70"><img class="header-logo" src="data:image/jpeg;base64,{{ $logoBase64 }}" alt="Logo"></td>
            <td>
                <div class="header-title">Rehavit&eacute;</div>
                <div class="header-sub">Expediente Cl&iacute;nico &mdash; {{ $patient->full_name }}</div>
            </td>
            <td class="header-meta">
                Exp: <strong>{{ $patient->expediente_id }}</strong><br>
                {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
</div>

{{-- ══ DATOS PERSONALES ══ --}}
<div class="section">
    <div class="section-title">1. Datos Personales</div>
    <table class="grid-2">
        <tr>
            <td class="col">
                <div class="field"><span class="field-label">Nombre completo</span><span class="field-value">{{ $patient->full_name }}</span></div>
                <div class="field"><span class="field-label">Fecha de nacimiento</span><span class="field-value">{{ $patient->birth_date?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Edad</span><span class="field-value">{{ $patient->age ?? '—' }} a&ntilde;os</span></div>
                <div class="field"><span class="field-label">G&eacute;nero</span><span class="field-value">{{ ucfirst($patient->gender ?? '—') }}</span></div>
                <div class="field"><span class="field-label">Estado Civil</span><span class="field-value">{{ $patient->marital_status ?? '—' }}</span></div>
            </td>
            <td class="col">
                <div class="field"><span class="field-label">Tel&eacute;fono</span><span class="field-value">{{ $patient->phone ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Correo electr&oacute;nico</span><span class="field-value">{{ $patient->email ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Fisioterapeuta asignado</span><span class="field-value">{{ $patient->user?->name ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Cuidador Primario</span><span class="field-value">{{ $patient->primary_caregiver ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Fecha de registro</span><span class="field-value">{{ $patient->created_at->format('d/m/Y') }}</span></div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ EXPEDIENTE MEDICO ══ --}}
<div class="section">
    <div class="section-title">2. Expediente M&eacute;dico</div>

    {{-- Datos generales --}}
    <table class="grid-2">
        <tr>
            <td class="col">
                <div class="field"><span class="field-label">Ocupaci&oacute;n</span><span class="field-value">{{ $patient->occupation ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Lateralidad</span><span class="field-value">{{ $patient->laterality ?? '—' }}</span></div>
                <div class="field"><span class="field-label">Dependencia econ&oacute;mica</span><span class="field-value">{{ $patient->economic_dependency ?? '—' }}</span></div>
            </td>
            <td class="col">
                <div class="field"><span class="field-label">Direcci&oacute;n</span><span class="field-value">{{ $patient->address ?? '—' }}</span></div>
            </td>
        </tr>
    </table>

    @if($patient->observations)
    <div class="field"><span class="field-label">Observaciones del Fisioterapeuta</span><span class="field-value">{{ $patient->observations }}</span></div>
    @endif

    {{-- Representante Legal --}}
    @if($patient->guardian_name)
    <div class="sub-title">Representante Legal</div>
    <table class="grid-2">
        <tr>
            <td class="col">
                <div class="field"><span class="field-label">Nombre</span><span class="field-value">{{ $patient->guardian_name }}</span></div>
                <div class="field"><span class="field-label">Fecha de nacimiento</span><span class="field-value">{{ $patient->guardian_birth_date?->format('d/m/Y') ?? '—' }}</span></div>
            </td>
            <td class="col">
                <div class="field"><span class="field-label">Edad</span><span class="field-value">{{ $patient->guardian_age ?? '—' }} a&ntilde;os</span></div>
                <div class="field"><span class="field-label">G&eacute;nero</span><span class="field-value">{{ $patient->guardian_gender ?? '—' }}</span></div>
            </td>
        </tr>
    </table>
    @endif

    {{-- Antecedentes Personales No Patológicos --}}
    @php $np = $patient->personal_non_pathological ?? []; @endphp
    @if(array_filter($np))
    <div class="sub-title">Antecedentes Personales No Patol&oacute;gicos</div>
    <table class="json-grid">
        @foreach($np as $key => $val) @if($val)
        <tr><td class="json-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</td><td class="json-val">{{ $val }}</td></tr>
        @endif @endforeach
    </table>
    @endif

    {{-- Antecedentes Heredofamiliares --}}
    @php $fh = $patient->family_history ?? []; @endphp
    @if(array_filter($fh))
    <div class="sub-title">Antecedentes Heredofamiliares</div>
    <table class="json-grid">
        @foreach($fh as $key => $val) @if($val)
        <tr><td class="json-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</td><td class="json-val">{{ $val }}</td></tr>
        @endif @endforeach
    </table>
    @endif

    {{-- Antecedentes Personales Patológicos --}}
    @php $pp = $patient->personal_pathological ?? []; @endphp
    @if(array_filter($pp))
    <div class="sub-title">Antecedentes Personales Patol&oacute;gicos</div>
    <table class="json-grid">
        @foreach($pp as $key => $val) @if($val)
        <tr><td class="json-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</td><td class="json-val">{{ $val }}</td></tr>
        @endif @endforeach
    </table>
    @endif

    {{-- Interrogatorio por Aparatos y Sistemas --}}
    @php $sr = $patient->systems_review ?? []; @endphp
    @if(array_filter($sr))
    <div class="sub-title">Interrogatorio por Aparatos y Sistemas</div>
    <table class="json-grid">
        @foreach($sr as $key => $val) @if($val)
        <tr><td class="json-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</td><td class="json-val">{{ $val }}</td></tr>
        @endif @endforeach
    </table>
    @endif
</div>

{{-- ══ DIAGNÓSTICO INICIAL ══ --}}
<div class="section">
    <div class="section-title">3. Diagn&oacute;stico Inicial</div>
    <div class="diag-box">{{ $patient->diagnosis ?? 'Sin diagnóstico registrado.' }}</div>
</div>

{{-- ══ CONSULTAS ══ --}}
@if($consultations->count())
<div class="section">
    <div class="section-title">4. Consultas ({{ $consultations->count() }})</div>

    @foreach($consultations as $i => $c)
    @php
        $statusLabel = match($c->status) {
            'open'        => 'Abierta',
            'in_progress' => 'En Progreso',
            'closed'      => 'Cerrada',
            default       => ucfirst($c->status),
        };
        $cc = $c->current_condition ?? [];
        $pe = $c->physical_exam ?? [];
    @endphp

    <div class="consulta-block">
        <table class="consulta-hdr" width="100%">
            <tr>
                <td class="ch-title">Consulta {{ $i + 1 }}</td>
                <td class="ch-status"><span class="badge-{{ $c->status }}">{{ $statusLabel }}</span></td>
                <td class="ch-date">{{ $c->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="consulta-body">
            {{-- 1. Motivo de Consulta --}}
            @if($c->reason)
            <div class="field"><span class="field-label">Motivo de consulta</span><span class="field-value">{{ $c->reason }}</span></div>
            @endif

            {{-- 2. Padecimiento actual --}}
            @if(array_filter($cc))
            <div class="sub-title" style="margin-top:6px;">Padecimiento Actual</div>
            <table class="json-grid">
                @php
                    $ccOrder = [
                        'inicio'                  => 'Inicio',
                        'mecanismo_lesion'        => 'Mecanismo de Lesión',
                        'evolucion'               => 'Evolución',
                        'tratamiento_actual'      => 'Tratamiento Actual',
                        'limitacion_movimiento'   => 'Limitación del Movimiento',
                        'eva'                     => 'EVA (Escala Visual Analógica)',
                        'fuerza'                  => 'Fuerza',
                        'actividad_fisica'        => 'Actividad Física',
                        'tipo_dolor'              => 'Tipo de Dolor',
                        'comportamiento_sintomas' => 'Comportamiento de los Síntomas',
                        'factores_alivian'        => 'Factores que Alivian las Molestias',
                        'cambio_peso'             => 'Pérdida/Ganancia de Peso Repentina',
                        'cambios_apetito'         => 'Cambios en el Apetito',
                        'debilidad_astenia'       => 'Debilidad o Astenia',
                        'incontinencia'           => 'Incontinencia Fecal/Urinaria',
                        'otros'                   => 'Otros',
                    ];
                @endphp
                @foreach($ccOrder as $key => $label) @if(!empty($cc[$key]))
                <tr><td class="json-key">{{ $label }}</td><td class="json-val">{{ $cc[$key] }}</td></tr>
                @endif @endforeach
            </table>
            @endif

            {{-- 3. Estudios previos --}}
            @if($c->previous_studies_notes)
            <div class="field" style="margin-top:6px;"><span class="field-label">Estudios previos</span><span class="field-value">{{ $c->previous_studies_notes }}</span></div>
            @endif

            {{-- 4. Exploración física --}}
            @if(array_filter($pe))
            <div class="sub-title" style="margin-top:6px;">Exploraci&oacute;n F&iacute;sica</div>
            <table class="json-grid">
                @php
                    $peOrder = [
                        'temperatura'              => 'Temperatura (°C)',
                        'frecuencia_cardiaca'      => 'Frecuencia Cardíaca (lpm)',
                        'tension_arterial'         => 'Tensión Arterial (mmHg)',
                        'frecuencia_respiratoria'  => 'Frecuencia Respiratoria (rpm)',
                        'saturacion_oxigeno'       => 'Saturación de Oxígeno (%)',
                        'peso'                     => 'Peso (kg)',
                        'altura'                   => 'Altura (cm)',
                        'traslado'                 => 'Traslado',
                        'tipo_marcha'              => 'Tipo de Marcha',
                        'pruebas_complementarias'  => 'Pruebas Complementarias',
                        'otros'                    => 'Otros',
                    ];
                @endphp
                @foreach($peOrder as $key => $label) @if(!empty($pe[$key]))
                <tr><td class="json-key">{{ $label }}</td><td class="json-val">{{ $pe[$key] }}</td></tr>
                @endif @endforeach
            </table>
            @endif

            {{-- 5. Banderas Clínicas --}}
            @php
                $flags = $c->clinical_flags ?? [];
                $flagLabels = [
                    'rojas'    => '🚩 Banderas Rojas',
                    'amarillas'=> '🟡 Banderas Amarillas',
                    'azules'   => '🔵 Banderas Azules',
                    'negras'   => '⚫ Banderas Negras',
                    'verdes'   => '🟢 Banderas Verdes',
                    'blancas'  => '⚪ Banderas Blancas',
                ];
                $hasFlags = collect($flags)->except('observaciones')->filter(fn($v) => is_array($v) && count($v) > 0)->isNotEmpty();
            @endphp
            @if($hasFlags)
            <div class="sub-title" style="margin-top:6px;">Banderas Clínicas</div>
            <table class="json-grid">
                @foreach($flagLabels as $fkey => $flabel)
                    @if(!empty($flags[$fkey]) && is_array($flags[$fkey]))
                        <tr>
                            <td class="json-key">{{ $flabel }}</td>
                            <td class="json-val">{{ implode(', ', $flags[$fkey]) }}</td>
                        </tr>
                    @endif
                @endforeach
                @if(!empty($flags['observaciones']))
                    <tr>
                        <td class="json-key">Observaciones de Banderas</td>
                        <td class="json-val">{{ $flags['observaciones'] }}</td>
                    </tr>
                @endif
            </table>
            @endif

            {{-- 6. Diagnóstico --}}
            @if($c->diagnosis)
            <div class="field" style="margin-top:6px;"><span class="field-label">Diagn&oacute;stico fisioter.</span><span class="field-value">{{ $c->diagnosis }}</span></div>
            @endif

            {{-- 7. Pronóstico --}}
            @if($c->prognosis)
            <div class="field" style="margin-top:6px;"><span class="field-label">Pron&oacute;stico</span><span class="field-value">{{ $c->prognosis }}</span></div>
            @endif

            {{-- 8. Objetivos de tratamiento --}}
            @if($c->treatment_objectives)
            <div class="field" style="margin-top:6px;"><span class="field-label">Objetivos de tratamiento</span><span class="field-value">{{ $c->treatment_objectives }}</span></div>
            @endif

            {{-- 9. Alta Fisioterapéutica --}}
            @if($c->status === 'closed' || $c->status === 'alta_clinica')
                @if($c->discharge_recommendations)
                <div class="field" style="margin-top:6px;"><span class="field-label">Recomendaciones al alta</span><span class="field-value">{{ $c->discharge_recommendations }}</span></div>
                @endif
                @if($c->discharge_reason)
                <div class="field" style="margin-top:6px;"><span class="field-label">Motivo de alta</span><span class="field-value">{{ $c->discharge_reason }}</span></div>
                @endif
            @endif

            {{-- Notas de evolución --}}
            @if($c->evolutionNotes->count())
            <div class="notes-title">Notas de Evoluci&oacute;n ({{ $c->evolutionNotes->count() }})</div>
            @foreach($c->evolutionNotes as $note)
            @php $vs = $note->vital_signs ?? []; @endphp
            <div class="note-block">
                <div class="note-meta">
                    Sesi&oacute;n {{ $note->session_number ?? '—' }}
                    &nbsp;&middot;&nbsp;
                    {{ $note->date ? $note->date->format('d/m/Y') : $note->created_at->format('d/m/Y') }}
                </div>
                @if($note->description)<div class="note-row"><span class="note-lbl">Descripci&oacute;n: </span>{{ $note->description }}</div>@endif
                @if($note->plan)<div class="note-row"><span class="note-lbl">Plan: </span>{{ $note->plan }}</div>@endif
                @if($note->treatment)<div class="note-row"><span class="note-lbl">Tratamiento: </span>{{ $note->treatment }}</div>@endif
                @if($note->additional_notes)<div class="note-row"><span class="note-lbl">Notas adicionales: </span>{{ $note->additional_notes }}</div>@endif
                @if($note->comments)<div class="note-row"><span class="note-lbl">Comentarios: </span>{{ $note->comments }}</div>@endif
                @if(array_filter($vs))
                <table class="vitals-table" style="margin-top:4px;">
                    <tr>
                        <th>Signo</th><th>Inicio</th><th>Final</th>
                    </tr>
                    @if(!empty($vs['temp_start']) || !empty($vs['temp_end']))
                    <tr><td>Temperatura</td><td>{{ $vs['temp_start'] ?? '—' }}</td><td>{{ $vs['temp_end'] ?? '—' }}</td></tr>
                    @endif
                    @if(!empty($vs['saturation_start']) || !empty($vs['saturation_end']))
                    <tr><td>Saturaci&oacute;n O2</td><td>{{ $vs['saturation_start'] ?? '—' }}</td><td>{{ $vs['saturation_end'] ?? '—' }}</td></tr>
                    @endif
                    @if(!empty($vs['bp_start']) || !empty($vs['bp_end']))
                    <tr><td>Tensi&oacute;n arterial</td><td>{{ $vs['bp_start'] ?? '—' }}</td><td>{{ $vs['bp_end'] ?? '—' }}</td></tr>
                    @endif
                    @if(!empty($vs['hr_start']) || !empty($vs['hr_end']))
                    <tr><td>Frec. cardiaca</td><td>{{ $vs['hr_start'] ?? '—' }}</td><td>{{ $vs['hr_end'] ?? '—' }}</td></tr>
                    @endif
                </table>
                @endif
            </div>
            @endforeach
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="footer">
    Rehavit&eacute; &mdash; Sistema de Gesti&oacute;n Cl&iacute;nica &mdash; Generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }} hrs
</div>

</body>
</html>

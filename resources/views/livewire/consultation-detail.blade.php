<div x-data="{ showDeleteModal: false }">
    @if($this->consultation)
        @php $consultation = $this->consultation; @endphp
        @if($editingConsultationId)
        <div id="edit-consultation-modal"
             x-data="{}"
             x-init="document.body.style.overflow = 'hidden'; return () => { document.body.style.overflow = ''; }"
             onclick="if(event.target===this) {
                 const form = document.getElementById('edit-consultation-form');
                 if (form && form.dataset.dirty === 'true') {
                     if (!confirm('Tienes cambios sin guardar en la consulta. ¿Seguro que deseas salir sin guardar?')) {
                         return;
                     }
                 }
                 if (form) { delete form.dataset.dirty; }
                 @this.cancelEdit();
             }"
             style="position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); padding:24px; overflow-y:auto; display:flex; align-items:flex-start; justify-content:center;">
             
             <div style="background:var(--bg-app); max-width:1100px; width:100%; margin:40px auto; border-radius:16px; padding:32px; position:relative; box-shadow:0 30px 80px rgba(0,0,0,0.6);">
                 
                 {{-- Modal Header --}}
                 <div class="flex-between" style="margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:16px;">
                     <h2 style="margin:0; font-size:20px; font-weight:700;"><i class="fas fa-pen"></i> Editar Episodio</h2>
                     <div style="display:flex; align-items:center; gap:10px;">
                         <button type="submit" form="edit-consultation-form" class="btn-modern btn-primary btn-sm" wire:loading.attr="disabled" wire:target="saveConsultation">
                             <i class="fas fa-save" wire:loading.remove wire:target="saveConsultation"></i>
                             <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveConsultation"></i>
                             Guardar Cambios
                         </button>
                         <button type="button"
                                 onclick="const form = document.getElementById('edit-consultation-form'); if(form && form.dataset.dirty === 'true' && !confirm('Tienes cambios sin guardar en la consulta. ¿Seguro que deseas salir sin guardar?')) return; if(form) { delete form.dataset.dirty; } @this.cancelEdit();"
                                 style="background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); cursor:pointer;">
                             <i class="fas fa-times"></i>
                         </button>
                     </div>
                 </div>

                 <form id="edit-consultation-form" wire:submit.prevent="saveConsultation"
                       oninput="this.dataset.dirty = 'true'"
                       onchange="this.dataset.dirty = 'true'"
                       onsubmit="delete this.dataset.dirty">

                     {{-- Estado --}}
                     <div class="form-group">
                         <label class="form-label">Estado de la Consulta</label>
                         <select wire:model="status" class="form-control-modern">
                             <option value="">Seleccionar...</option>
                             <option value="valoracion">En Valoración</option>
                             <option value="activo">Activo</option>
                             <option value="seguimiento">Seguimiento</option>
                             <option value="alta_clinica">Alta Clínica</option>
                             <option value="suspendido">Suspendido</option>
                             <option value="abandono">Abandono Terapéutico</option>
                             <option value="referido">Referido</option>
                             <option value="inactivo">Expediente Inactivo</option>
                         </select>
                     </div>

                     {{-- ─── 1. MOTIVO DE CONSULTA ─────────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-question-circle" style="color:var(--accent-primary);"></i>
                             <h3 style="display:inline; margin:0;">Motivo de Consulta</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Descripción *</label>
                                 <textarea wire:model="reason" class="form-control-modern" rows="3" required spellcheck="true" placeholder="Describa el motivo principal de la consulta..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 2. PADECIMIENTO ACTUAL ─────────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-heartbeat" style="color:#f59e0b;"></i>
                             <h3 style="display:inline; margin:0;">Padecimiento Actual</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div class="grid grid-2" style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Inicio</label>
                                 <input type="text" wire:model="current_condition.inicio" class="form-control-modern" style="text-transform:capitalize;" placeholder="¿Cuándo inició? ¿Cómo?">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Mecanismo de Lesión</label>
                                 <input type="text" wire:model="current_condition.mecanismo_lesion" class="form-control-modern" style="text-transform:capitalize;" placeholder="Traumático, insidioso, postural...">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Evolución</label>
                                 <input type="text" wire:model="current_condition.evolucion" class="form-control-modern" style="text-transform:capitalize;" placeholder="Mejora, empeora, estable...">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Tratamiento Actual</label>
                                 <input type="text" wire:model="current_condition.tratamiento_actual" class="form-control-modern" style="text-transform:capitalize;" placeholder="Medicamentos, fisioterapia previa...">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Limitación del Movimiento</label>
                                 <input type="text" wire:model="current_condition.limitacion_movimiento" class="form-control-modern" style="text-transform:capitalize;">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">EVA (Escala Visual Analógica)</label>
                                 <input type="text" wire:model="current_condition.eva" class="form-control-modern" placeholder="0-10">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Fuerza</label>
                                 <input type="text" wire:model="current_condition.fuerza" class="form-control-modern" style="text-transform:capitalize;" placeholder="5/5, 4/5...">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Actividad Física</label>
                                 <input type="text" wire:model="current_condition.actividad_fisica" class="form-control-modern" style="text-transform:capitalize;" placeholder="Sedentario, moderado, activo...">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Tipo de Dolor</label>
                                 <input type="text" wire:model="current_condition.tipo_dolor" class="form-control-modern" style="text-transform:capitalize;" placeholder="Ej: Punzante, quemante...">
                                 <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:2px;">Sugerencias: Punzante, quemante, calambre, sordo</span>
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Comportamiento de los Síntomas</label>
                                 <textarea wire:model="current_condition.comportamiento_sintomas" class="form-control-modern" rows="2" spellcheck="true" placeholder="Día/noche/parado/sentado..."></textarea>
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Factores que Alivian las Molestias</label>
                                 <input type="text" wire:model="current_condition.factores_alivian" class="form-control-modern" style="text-transform:capitalize;" placeholder="Ej: Reposo, calor, frío, estiramientos...">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Pérdida/Ganancia de Peso Repentina</label>
                                 <input type="text" wire:model="current_condition.cambio_peso" class="form-control-modern" style="text-transform:capitalize;" placeholder="Sí / No / Descripción">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Cambios en el Apetito</label>
                                 <input type="text" wire:model="current_condition.cambios_apetito" class="form-control-modern" style="text-transform:capitalize;" placeholder="Sí / No / Descripción">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Debilidad o Astenia</label>
                                 <input type="text" wire:model="current_condition.debilidad_astenia" class="form-control-modern" style="text-transform:capitalize;" placeholder="Sí / No / Descripción">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Incontinencia Fecal/Urinaria</label>
                                 <input type="text" wire:model="current_condition.incontinencia" class="form-control-modern" style="text-transform:capitalize;" placeholder="Sí / No / Descripción">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Otros <span style="color:var(--text-muted); font-weight:400;">(hematoma, postura, marcha, signos de inflamación)</span></label>
                                 <textarea wire:model="current_condition.otros" class="form-control-modern" rows="2" spellcheck="true" placeholder="Edema, hematoma, alteración postural..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 3. RESULTADOS PREVIOS O ACTUALES DE ESTUDIOS DE IMAGEN ── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-x-ray" style="color:#3b82f6;"></i>
                             <h3 style="display:inline; margin:0;">Resultados Previos o Actuales de Estudios de Imagen</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Descripción</label>
                                 <textarea wire:model="previous_studies_notes" class="form-control-modern" rows="3" spellcheck="true" placeholder="Rayos X, resonancia, ultrasonido... hallazgos relevantes"></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 4. EXPLORACIÓN FÍSICA ──────────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-user-md" style="color:#10b981;"></i>
                             <h3 style="display:inline; margin:0;">Exploración Física</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div class="grid grid-2" style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Temperatura (°C)</label>
                                 <input type="text" wire:model="physical_exam.temperatura" class="form-control-modern" placeholder="36.6">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Frecuencia Cardíaca (lpm)</label>
                                 <input type="text" wire:model="physical_exam.frecuencia_cardiaca" class="form-control-modern" placeholder="72">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Tensión Arterial (mmHg)</label>
                                 <input type="text" wire:model="physical_exam.tension_arterial" class="form-control-modern" placeholder="120/80">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Frecuencia Respiratoria (rpm)</label>
                                 <input type="text" wire:model="physical_exam.frecuencia_respiratoria" class="form-control-modern" placeholder="16">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Saturación de Oxígeno (%)</label>
                                 <input type="text" wire:model="physical_exam.saturacion_oxigeno" class="form-control-modern" placeholder="98">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Peso (kg)</label>
                                 <input type="text" wire:model="physical_exam.peso" class="form-control-modern" placeholder="70">
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Altura (cm)</label>
                                 <input type="text" wire:model="physical_exam.altura" class="form-control-modern" placeholder="170">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Traslado</label>
                                 <input type="text" wire:model="physical_exam.traslado" class="form-control-modern" spellcheck="true" placeholder="Ambulatorio, silla de ruedas...">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Tipo de Marcha</label>
                                 <input type="text" wire:model="physical_exam.tipo_marcha" class="form-control-modern" spellcheck="true" placeholder="Normal, antálgica, espástica...">
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Pruebas Complementarias</label>
                                 <textarea wire:model="physical_exam.pruebas_complementarias" class="form-control-modern" rows="2" spellcheck="true" placeholder="Goniometría, ASIA, Godet, pruebas musculares..."></textarea>
                             </div>
                             <div class="form-group" style="grid-column: 1 / -1;">
                                 <label class="form-label">Otros</label>
                                 <textarea wire:model="physical_exam.otros" class="form-control-modern" rows="2" spellcheck="true" placeholder="Observaciones adicionales de la exploración..."></textarea>
                                 <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:2px;">Sugerencias: Funcionalidad, Examen postural, Estado de alerta, Actitud durante la consulta, Equilibrio, Sensibilidad</span>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 4. BANDERAS CLÍNICAS ────────────────────────── --}}
                     <details class="consultation-section" style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-flag" style="color:#ef4444;"></i>
                             <h3 style="display:inline; margin:0;">Banderas Clínicas</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px; display:flex; flex-direction:column; gap:20px;">
                             @php
                                 $flagOptions = [
                                     'rojas' => [
                                         'label' => '🚩 Banderas Rojas', 'color' => '#ef4444',
                                         'items' => ['Pérdida de peso inexplicable','Fiebre persistente','Dolor nocturno severo','Dolor constante no mecánico','Antecedente de cáncer','Incontinencia urinaria/fecal reciente','Anestesia en "silla de montar"','Debilidad progresiva','Trauma severo','Sospecha de fractura','Signos neurológicos progresivos','Disnea o dolor torácico'],
                                     ],
                                     'amarillas' => [
                                         'label' => '🟡 Banderas Amarillas', 'color' => '#f59e0b',
                                         'items' => ['Miedo al movimiento (kinesiofobia)','Catastrofización del dolor','Ansiedad','Depresión','Estrés elevado','Mala adherencia','Creencia de "no voy a mejorar"','Dependencia excesiva del tratamiento pasivo'],
                                     ],
                                     'azules' => [
                                         'label' => '🔵 Banderas Azules', 'color' => '#3b82f6',
                                         'items' => ['Insatisfacción laboral','Ambiente laboral estresante','Trabajo físicamente demandante','Miedo a regresar al trabajo','Problemas ergonómicos','Baja motivación laboral','Conflictos con empresa'],
                                     ],
                                     'negras' => [
                                         'label' => '⚫ Banderas Negras', 'color' => '#6b7280',
                                         'items' => ['Problemas legales/laborales','Compensación económica por lesión','Incapacidad prolongada','Dificultad económica para continuar tratamiento','Falta de acceso a atención médica'],
                                     ],
                                     'verdes' => [
                                         'label' => '🟢 Banderas Verdes', 'color' => '#10b981',
                                         'items' => ['Buena adherencia','Motivación alta','Red de apoyo familiar','Buena comprensión del tratamiento','Actividad física previa'],
                                     ],
                                     'blancas' => [
                                         'label' => '⚪ Banderas Blancas', 'color' => '#e5e7eb',
                                         'items' => ['Sobreprotección familiar','Dependencia funcional aprendida','Falta de accesibilidad','Aislamiento social'],
                                     ],
                                 ];
                             @endphp
                             @foreach($flagOptions as $flagKey => $flagData)
                                 <div style="border:1px solid {{ $flagData['color'] }}33; border-radius:10px; padding:12px;">
                                     <div style="font-weight:600; font-size:14px; color:{{ $flagData['color'] }}; margin-bottom:10px;">{{ $flagData['label'] }}</div>
                                     <div class="grid grid-2" style="gap:6px;">
                                         @foreach($flagData['items'] as $item)
                                             <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:var(--text-secondary);">
                                                 <input type="checkbox"
                                                        wire:model="clinical_flags.{{ $flagKey }}"
                                                        value="{{ $item }}"
                                                        style="accent-color:{{ $flagData['color'] }}; width:16px; height:16px;">
                                                 {{ $item }}
                                             </label>
                                         @endforeach
                                     </div>
                                 </div>
                             @endforeach
                             <div class="form-group" style="margin-top:4px;">
                                 <label class="form-label">Observaciones de Banderas</label>
                                 <textarea wire:model="clinical_flags.observaciones" class="form-control-modern" rows="2" placeholder="Notas adicionales sobre las banderas clínicas..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 5. PADECIMIENTO ─────────────────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-diagnoses" style="color:#8b5cf6;"></i>
                             <h3 style="display:inline; margin:0;">Diagnóstico Fisioterapéutico</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Descripción</label>
                                 <textarea wire:model="diagnosis" class="form-control-modern" rows="3" spellcheck="true" placeholder="Padecimiento / Diagnóstico fisioterapéutico..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 6. PRONÓSTICO ──────────────────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-chart-line" style="color:#06b6d4;"></i>
                             <h3 style="display:inline; margin:0;">Pronóstico</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Descripción</label>
                                 <textarea wire:model="prognosis" class="form-control-modern" rows="2" spellcheck="true" placeholder="Favorable, reservado, desfavorable..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 7. OBJETIVOS DE TRATAMIENTO ────────────────── --}}
                     <details class="consultation-section" open style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-bullseye" style="color:#f97316;"></i>
                             <h3 style="display:inline; margin:0;">Objetivos de Tratamiento</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div style="margin-top:12px;">
                             <div class="form-group">
                                 <textarea wire:model="treatment_objectives" class="form-control-modern" rows="3" spellcheck="true" placeholder="Reducir dolor, mejorar movilidad, recuperar función..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- ─── 8. ALTA FISIOTERAPÉUTICA ───────────────────── --}}
                     <details class="consultation-section" style="margin-top:16px;">
                         <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                             <i class="fas fa-door-open" style="color:#10b981;"></i>
                             <h3 style="display:inline; margin:0;">Alta Fisioterapéutica</h3>
                             <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                         </summary>
                         <div class="grid grid-2" style="margin-top:12px;">
                             <div class="form-group">
                                 <label class="form-label">Motivo de Alta</label>
                                 <textarea wire:model="discharge_reason" class="form-control-modern" rows="2" spellcheck="true" placeholder="Alta por objetivos cumplidos, por abandono..."></textarea>
                             </div>
                             <div class="form-group">
                                 <label class="form-label">Recomendaciones</label>
                                 <textarea wire:model="discharge_recommendations" class="form-control-modern" rows="2" spellcheck="true" placeholder="Programa de mantenimiento, ejercicio en casa..."></textarea>
                             </div>
                         </div>
                     </details>

                     {{-- Botones guardar / cancelar --}}
                     <div style="display:flex;gap:12px;justify-content:center;margin-top:24px;">
                         <button type="button" class="btn-modern btn-secondary btn-sm"
                                 onclick="const form = document.getElementById('edit-consultation-form'); if(form && form.dataset.dirty === 'true' && !confirm('Tienes cambios sin guardar en la consulta. ¿Seguro que deseas salir sin guardar?')) return; if(form) { delete form.dataset.dirty; } @this.cancelEdit();"
                                 style="flex:1; max-width:200px; text-align:center; justify-content:center;">Cancelar</button>
                         <button type="submit" id="save-consultation-submit-btn" class="btn-modern btn-primary btn-sm" style="flex:1; max-width:200px; text-align:center; justify-content:center;" wire:loading.attr="disabled" wire:target="saveConsultation">
                             <i class="fas fa-save" wire:loading.remove wire:target="saveConsultation"></i>
                             <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveConsultation"></i>
                             Guardar Cambios
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    @endif

    <div class="mb-24 flex-between" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 4px 0; color:var(--accent-primary);">
                <i class="fas fa-notes-medical"></i> Detalle del Episodio
            </h2>
            <div style="font-size: 14px; color: var(--text-secondary);">
                Fecha: {{ $consultation->created_at->format('d/m/Y h:i A') }}
            </div>
        </div>
        <div style="display:flex; gap:8px;">
            <!--<button type="button" wire:click="$dispatch('clearSelectedConsultation')" class="btn-modern btn-secondary">-->
            <button type="button" wire:click="goBack" class="btn-modern btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la Lista
            </button>
            @if(auth()->user()->admin)
            <form action="{{ route('admin.consultations.destroy', $consultation->id) }}" method="POST" id="delete-consultation-form" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = true" class="btn-modern" style="background:rgba(239,68,68,0.1); color:var(--danger); border:1px solid rgba(239,68,68,0.2);">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" style="display:none; position:fixed; inset:0; z-index:9999;" x-transition.opacity>
        <div style="position:absolute; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px);" @click="showDeleteModal = false"></div>
        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; padding:16px; pointer-events:none;">
            <div style="pointer-events:auto; position:relative; z-index:10000; background:var(--bg-card); border:1px solid rgba(239,68,68,0.4); border-radius:16px; padding:32px; max-width:420px; width:100%; text-align:center; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
                <div style="font-size:48px; margin-bottom:16px; color:#ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 style="margin:0 0 8px; font-size:20px; font-weight:700; color:var(--text-primary);">Eliminar Consulta</h3>
                <p style="margin:0 0 24px; color:var(--text-secondary); font-size:14px; line-height:1.6;">
                    ¿Estás seguro de que deseas eliminar esta consulta de forma permanente? Esta acción no se puede deshacer.
                </p>
                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <button @click="showDeleteModal = false" type="button"
                            style="padding:10px 20px; border-radius:8px; background:rgba(255,255,255,0.05); color:var(--text-secondary); border:1px solid rgba(255,255,255,0.1); cursor:pointer; font-size:14px;">
                        Cancelar
                    </button>
                    <button onclick="document.getElementById('delete-consultation-form').submit();" type="button"
                            style="padding:10px 20px; border-radius:8px; background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); cursor:pointer; font-size:14px; font-weight:600;">
                        <i class="fas fa-trash"></i> Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session()->has('success'))
        <div class="alert-modern alert-success mb-16" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); padding:12px; border-radius:8px; display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert-modern alert-danger mb-16" style="background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.2); padding:12px; border-radius:8px; display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="glass-card mb-24" style="padding: 0; overflow: visible; position: relative; z-index: 20;" wire:key="consultation-tabs-container">
        @php
            $moreTabs = ['ejercicios', 'escalas', 'fotos'];
            $moreTabsLabels = [
                'ejercicios' => 'Ejercicios (' . $consultation->exercises->count() . ')',
                'escalas' => 'Escalas Clínicas (' . $consultation->clinicalScales->count() . ')',
                'fotos' => 'Fotos Clínicas (' . $consultation->clinicalPhotos->count() . ')',
            ];
        @endphp
        <div class="tabs-modern" style="border-bottom: 1px solid var(--border-color); display: flex; align-items:center;">
            <button class="tab-item {{ $activeTab === 'informacion' ? 'active' : '' }}" wire:click="$set('activeTab', 'informacion')">
                Información Principal
            </button>
            <button class="tab-item {{ $activeTab === 'notas' ? 'active' : '' }}" wire:click="$set('activeTab', 'notas')">
                Notas de Evolución ({{ $consultation->evolutionNotes->count() }})
            </button>
            <button class="tab-item {{ $activeTab === 'estudios' ? 'active' : '' }}" wire:click="$set('activeTab', 'estudios')">
                Estudios ({{ $consultation->studies->count() }})
            </button>
            <button class="tab-item {{ $activeTab === 'documentos' ? 'active' : '' }}" wire:click="$set('activeTab', 'documentos')">
                Documentos ({{ $consultation->clinicalDocuments->count() }})
            </button>

            <div x-data="{ openMoreTabs: false }" style="position:relative;">
                <button type="button" class="tab-item {{ in_array($activeTab, $moreTabs) ? 'active' : '' }}"
                        @click="openMoreTabs = !openMoreTabs" @click.outside="openMoreTabs = false">
                    <i class="fas fa-bars"></i> {{ in_array($activeTab, $moreTabs) ? $moreTabsLabels[$activeTab] : 'Más' }}
                    <i class="fas fa-caret-down" style="font-size:10px; margin-left:4px;"></i>
                </button>
                <div x-show="openMoreTabs" x-cloak x-transition
                     style="position:absolute; left:0; top:calc(100% + 4px); z-index:50; min-width:220px; background:var(--bg-app); border:1px solid rgba(255,255,255,0.1); border-radius:10px; box-shadow:0 12px 32px rgba(0,0,0,0.4); padding:6px; display:flex; flex-direction:column; gap:2px;">
                    @foreach($moreTabs as $tabKey)
                        <button type="button"
                                wire:click="$set('activeTab', '{{ $tabKey }}')" @click="openMoreTabs = false"
                                class="tab-item {{ $activeTab === $tabKey ? 'active' : '' }}"
                                style="text-align:left; width:100%; justify-content:flex-start;">
                            {{ $moreTabsLabels[$tabKey] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- TAB: INFORMACIÓN PRINCIPAL --}}
    <div wire:key="tab-wrapper-informacion" style="display: {{ $activeTab === 'informacion' ? 'block' : 'none' }};">
        <div class="glass-card mb-24">
            <div class="flex-between mb-24">
                <h3 style="margin:0;font-size:18px;font-weight:600;">
                    <i class="fas fa-notes-medical" style="color:var(--accent-primary)"></i> Detalles Clínicos
                </h3>
                @if(!$editingConsultationId && (auth()->user()->admin || $consultation->user_id === auth()->id()))
                <button class="btn-modern btn-secondary btn-sm" wire:click="editConsultation">
                    <i class="fas fa-pen"></i> Editar Información
                </button>
                @endif
            </div>

            @php
                $statusLabels = [
                    'valoracion'   => 'En Valoración',
                    'activo'       => 'Activo',
                    'seguimiento'  => 'Seguimiento',
                    'alta_clinica' => 'Alta Clínica',
                    'suspendido'   => 'Suspendido',
                    'abandono'     => 'Abandono Terapéutico',
                    'referido'     => 'Referido',
                    'inactivo'     => 'Expediente Inactivo',
                    'open'         => 'Activo',
                    'in_progress'  => 'En Progreso',
                    'closed'       => 'Alta Clínica',
                ];
                $currentStatusLabel = $statusLabels[$consultation->status] ?? ucfirst($consultation->status);
            @endphp

            @if(!$editingConsultationId)
                {{-- ══════════════════════════════════════════
                     VISTA DE SOLO LECTURA
                     ══════════════════════════════════════════ --}}

                {{-- Estado badge --}}
                <div class="mb-16">
                    <span class="badge-modern badge-{{ $consultation->status }}" style="font-size:13px; padding:5px 14px;">
                        {{ $currentStatusLabel }}
                    </span>
                </div>

                {{-- 1. MOTIVO DE CONSULTA --}}
                @if($consultation->reason)
                    <div style="margin-bottom:20px;">
                        <div style="font-weight:700; font-size:14px; color:var(--accent-primary); margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-question-circle"></i> Motivo de Consulta
                        </div>
                        <div class="data-pair">
                            <div class="data-label">Descripción</div>
                            <div class="data-value">{{ $consultation->reason }}</div>
                        </div>
                    </div>
                @endif

                {{-- 2. PADECIMIENTO ACTUAL --}}
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
                @if($consultation->current_condition && collect($consultation->current_condition)->filter()->count())
                    <details class="consultation-section" style="margin-top:16px;" open>
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-heartbeat" style="color:#f59e0b;"></i>
                            <h3 style="display:inline; margin:0;">Padecimiento Actual</h3>
                            <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div class="grid grid-2" style="margin-top:12px;">
                            @foreach($ccOrder as $key => $label)
                                @if(!empty($consultation->current_condition[$key]))
                                    <div class="data-pair" {!! in_array($key, ['comportamiento_sintomas','factores_alivian','otros']) ? 'style="grid-column:1/-1"' : '' !!}>
                                        <div class="data-label">{{ $label }}</div>
                                        <div class="data-value">{{ $consultation->current_condition[$key] }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </details>
                @endif

                {{-- 3. RESULTADOS PREVIOS O ACTUALES DE ESTUDIOS DE IMAGEN --}}
                @if($consultation->previous_studies_notes)
                    <details class="consultation-section" style="margin-top:16px;" open>
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-x-ray" style="color:#3b82f6;"></i>
                            <h3 style="display:inline; margin:0;">Resultados Previos o Actuales de Estudios de Imagen</h3>
                            <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div style="margin-top:12px;">
                            <div class="data-pair">
                                <div class="data-label">Descripción</div>
                                <div class="data-value">{{ $consultation->previous_studies_notes }}</div>
                            </div>
                        </div>
                    </details>
                @endif

                {{-- 4. EXPLORACIÓN FÍSICA --}}
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
                    $physicalExam = $consultation->physical_exam ?? [];
                @endphp
                @if($physicalExam && collect($physicalExam)->filter()->count())
                    <details class="consultation-section" style="margin-top:16px;" open>
                        <summary class="section-header" style="cursor:pointer; list-style:none; user-select:none; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-user-md" style="color:#10b981;"></i>
                            <h3 style="display:inline; margin:0;">Exploración Física</h3>
                            <i class="fas fa-chevron-down" style="margin-left:auto; font-size:12px; color:var(--text-muted);"></i>
                        </summary>
                        <div class="grid grid-2" style="margin-top:12px;">
                            @foreach($peOrder as $key => $label)
                                @if(!empty($physicalExam[$key]))
                                    <div class="data-pair" {!! in_array($key, ['traslado','tipo_marcha','pruebas_complementarias','otros']) ? 'style="grid-column:1/-1"' : '' !!}>
                                        <div class="data-label">{{ $label }}</div>
                                        <div class="data-value">{{ $physicalExam[$key] }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </details>
                @endif

                {{-- 4. BANDERAS CLÍNICAS --}}
                @php
                    $flags = $consultation->clinical_flags ?? [];
                    $flagColors = [
                        'rojas'    => '#ef4444',
                        'amarillas'=> '#f59e0b',
                        'azules'   => '#3b82f6',
                        'negras'   => '#6b7280',
                        'verdes'   => '#10b981',
                        'blancas'  => '#e5e7eb',
                    ];
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
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:600; font-size:15px; margin-bottom:12px; color:var(--text-primary);"><i class="fas fa-flag" style="color:#ef4444;"></i> Banderas Clínicas</div>
                        <div style="display:flex; flex-wrap:wrap; gap:10px;">
                            @foreach($flagLabels as $fkey => $flabel)
                                @if(!empty($flags[$fkey]) && is_array($flags[$fkey]))
                                    <div style="border:1px solid {{ $flagColors[$fkey] }}44; border-radius:8px; padding:8px 12px;">
                                        <div style="font-size:12px; font-weight:600; color:{{ $flagColors[$fkey] }}; margin-bottom:6px;">{{ $flabel }}</div>
                                        @foreach($flags[$fkey] as $item)
                                            <div style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:4px;">
                                                <i class="fas fa-check" style="color:{{ $flagColors[$fkey] }}; font-size:10px;"></i> {{ $item }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if(!empty($flags['observaciones']))
                            <div class="data-pair" style="margin-top:12px;">
                                <div class="data-label">Observaciones de Banderas</div>
                                <div class="data-value">{{ $flags['observaciones'] }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 5. DIAGNÓSTICO FISIOTERAPÉUTICO --}}
                @if($consultation->diagnosis)
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:700; font-size:14px; color:#8b5cf6; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-diagnoses"></i> Diagnóstico Fisioterapéutico
                        </div>
                        <div class="data-pair">
                            <div class="data-label">Descripción</div>
                            <div class="data-value">{{ $consultation->diagnosis }}</div>
                        </div>
                    </div>
                @endif

                {{-- 6. PRONÓSTICO --}}
                @if($consultation->prognosis)
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:700; font-size:14px; color:#06b6d4; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-chart-line"></i> Pronóstico
                        </div>
                        <div class="data-pair">
                            <div class="data-label">Descripción</div>
                            <div class="data-value">{{ $consultation->prognosis }}</div>
                        </div>
                    </div>
                @endif

                {{-- 7. OBJETIVOS DE TRATAMIENTO --}}
                @if($consultation->treatment_objectives)
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:700; font-size:14px; color:#f97316; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-bullseye"></i> Objetivos de Tratamiento
                        </div>
                        <div class="data-value" style="color:var(--text-secondary); font-size:14px; line-height:1.7;">{{ $consultation->treatment_objectives }}</div>
                    </div>
                @endif

                {{-- 8. ALTA FISIOTERAPÉUTICA --}}
                @if($consultation->discharge_recommendations || $consultation->discharge_reason)
                    <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div style="font-weight:700; font-size:14px; color:#10b981; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-door-open"></i> Alta Fisioterapéutica
                        </div>
                        <div class="grid grid-2">
                            @if($consultation->discharge_reason)
                                <div class="data-pair">
                                    <div class="data-label">Motivo de Alta</div>
                                    <div class="data-value">{{ $consultation->discharge_reason }}</div>
                                </div>
                            @endif
                            @if($consultation->discharge_recommendations)
                                <div class="data-pair">
                                    <div class="data-label">Recomendaciones</div>
                                    <div class="data-value">{{ $consultation->discharge_recommendations }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div wire:key="tab-wrapper-notas" style="display: {{ $activeTab === 'notas' ? 'block' : 'none' }};">
        @livewire('evolution-notes', ['consultationId' => $consultation->id], key('notes-'.$consultation->id))
    </div>

    <div wire:key="tab-wrapper-estudios" style="display: {{ $activeTab === 'estudios' ? 'block' : 'none' }};">
        @livewire('consultation-studies', ['consultationId' => $consultation->id], key('studies-'.$consultation->id))
    </div>

    <div wire:key="tab-wrapper-ejercicios" style="display: {{ $activeTab === 'ejercicios' ? 'block' : 'none' }};">
        @livewire('consultation-exercises', ['consultationId' => $consultation->id], key('exercises-'.$consultation->id))
    </div>

    <div wire:key="tab-wrapper-escalas" style="display: {{ $activeTab === 'escalas' ? 'block' : 'none' }};">
        @livewire('consultation-scales', ['consultationId' => $consultation->id], key('scales-'.$consultation->id))
    </div>

    <div wire:key="tab-wrapper-fotos" style="display: {{ $activeTab === 'fotos' ? 'block' : 'none' }}; text-align:left;">
        @livewire('consultation-photos', ['consultationId' => $consultation->id], key('photos-'.$consultation->id))
    </div>

    {{-- TAB: DOCUMENTOS (referencias al repositorio del paciente) --}}
    <div wire:key="tab-wrapper-documentos" style="display: {{ $activeTab === 'documentos' ? 'block' : 'none' }}; text-align:left;">
        <div class="glass-card mb-24">
            <p style="font-size:13px; color:var(--text-secondary); margin:0 0 16px;">
                Este episodio no almacena archivos propios: solo referencia documentos del repositorio
                <strong>Documentos Clínicos</strong> del paciente.
            </p>

            <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
                <select wire:model.live="selectedDocumentToLink" class="form-control-modern" style="max-width:360px; flex:1;">
                    <option value="">Vincular documento del repositorio...</option>
                    @foreach($this->availableDocumentsToLink as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->type }} — {{ $doc->date->format('d/m/Y') }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn-modern btn-primary btn-sm" wire:click="linkDocument" wire:loading.attr="disabled" @if(!$selectedDocumentToLink) disabled @endif>
                    <i class="fas fa-link"></i> Vincular
                </button>
                @if($this->availableDocumentsToLink->isEmpty())
                    <span style="font-size:12px; color:var(--text-muted);">No hay documentos del paciente disponibles para vincular (todos ya están vinculados, o el paciente aún no tiene documentos en "Documentos Clínicos").</span>
                @endif
            </div>

            @forelse($consultation->clinicalDocuments as $doc)
                <div class="flex-between" style="padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.06);" wire:key="linked-doc-{{ $doc->id }}">
                    <div>
                        <span class="badge-modern badge-info">{{ $doc->type }}</span>
                        <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">{{ $doc->date->format('d/m/Y') }}</span>
                        <span style="margin-left:8px;" title="{{ $doc->status_label }}">{{ $doc->status_emoji }} {{ $doc->status_label }}</span>
                        @if($doc->description)
                            <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">{{ $doc->description }}</div>
                        @endif
                    </div>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn-icon" title="Ver documento"><i class="fas fa-eye"></i></a>
                        <button type="button" wire:click="unlinkDocument({{ $doc->id }})" wire:confirm="¿Quitar la referencia de este documento en el episodio? El documento no se elimina del repositorio." class="btn-icon" title="Quitar del episodio">
                            <i class="fas fa-unlink"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding:24px;">
                    <i class="fas fa-folder-open"></i>
                    <p>Este episodio no tiene documentos vinculados todavía</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>

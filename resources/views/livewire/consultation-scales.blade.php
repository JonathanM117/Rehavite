<div>
    <div class="section-header" wire:key="scales-main-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fas fa-balance-scale" style="color:var(--accent-primary);"></i>
            <h3 style="margin:0;">Escalas Clínicas</h3>
        </div>
        <button class="btn-modern btn-primary btn-sm" type="button" wire:click="toggleForm">
            <i class="fas fa-plus"></i> Nueva Evaluación
        </button>
    </div>

    @if(session()->has('success'))
        <div class="alert-modern alert-success mb-24" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); padding:12px; border-radius:8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert-modern alert-danger mb-24" style="background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.2); padding:12px; border-radius:8px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Form --}}
    @if ($showForm)
        <div class="glass-card mb-24" style="padding:24px; border:1px solid rgba(255,255,255,0.08);">
            <div style="display:flex; gap:16px; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:12px;">
                <h4 style="margin:0; font-size:16px; font-weight:700;">{{ $editingScaleId ? 'Editar Evaluación' : 'Nueva Evaluación' }}</h4>
                
                <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                    <span style="font-size:12px; color:var(--text-secondary);">Tipo de Escala:</span>
                    <select wire:model.live="scaleType" class="form-control-modern" style="padding:4px 8px; font-size:12px; width:200px;" @if($editingScaleId) disabled @endif>
                        <option value="tinetti">Escala de Tinetti (Marcha/Equilibrio)</option>
                        <option value="borg_6mwt">Escala de Borg y Caminata 6 Min</option>
                    </select>
                </div>

                <div style="display:flex; gap:8px; align-items:center;">
                    <span style="font-size:12px; color:var(--text-secondary);">Fecha:</span>
                    <input type="date" wire:model="date" class="form-control-modern" style="padding:4px 8px; font-size:12px; width:130px;">
                </div>
            </div>

            <form wire:submit.prevent="saveScale">
                {{-- Tinetti Layout --}}
                @if ($scaleType === 'tinetti')
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:20px;">
                        {{-- Equilibrio (Balance) --}}
                        <div>
                            <h5 style="margin:0 0 12px; color:var(--accent-primary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:4px; font-size:14px; font-weight:600;">Sección 1: Equilibrio (Máx. 16)</h5>
                            <div style="display:flex; flex-direction:column; gap:10px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">1. Equilibrio Sentado</label>
                                    <select wire:model="tinetti_balance.sitting" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Se inclina o desliza en la silla</option>
                                        <option value="1">1 - Firme y seguro</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">2. Levantarse</label>
                                    <select wire:model="tinetti_balance.rising" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Incapaz sin ayuda</option>
                                        <option value="1">1 - Capaz, pero requiere usar brazos</option>
                                        <option value="2">2 - Capaz sin usar brazos</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">3. Intentos para Levantarse</label>
                                    <select wire:model="tinetti_balance.attempts" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Incapaz sin ayuda</option>
                                        <option value="1">1 - Capaz, pero necesita más de un intento</option>
                                        <option value="2">2 - Capaz en el primer intento</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">4. Equilibrio Inmediato al Levantarse (5 seg)</label>
                                    <select wire:model="tinetti_balance.immediate_standing" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Inestable, vacila, se tambalea</option>
                                        <option value="1">1 - Estable pero requiere andador/bastón/soporte</option>
                                        <option value="2">2 - Estable sin soporte</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">5. Equilibrio al Bipedestar (Parado)</label>
                                    <select wire:model="tinetti_balance.standing_balance" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Inestable</option>
                                        <option value="1">1 - Estable pero con base ancha (pies separados) o soporte</option>
                                        <option value="2">2 - Estable, pies juntos y sin soporte</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">6. Empujón en el Esternón</label>
                                    <select wire:model="tinetti_balance.nudged" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Comienza a caerse</option>
                                        <option value="1">1 - Se balancea, se agarra, pero se mantiene solo</option>
                                        <option value="2">2 - Estable, firme</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">7. Ojos Cerrados (Pies Juntos)</label>
                                    <select wire:model="tinetti_balance.eyes_closed" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Inestable o tambalea</option>
                                        <option value="1">1 - Estable</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">8. Giro de 360 Grados</label>
                                    <select wire:model="tinetti_balance.turning" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Pasos discontinuos o inestabilidad</option>
                                        <option value="1">1 - Pasos continuos pero vacila</option>
                                        <option value="2">2 - Seguro, continuo y estable</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">9. Sentarse</label>
                                    <select wire:model="tinetti_balance.sitting_down" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Inseguro, calcula mal la distancia o cae bruscamente</option>
                                        <option value="1">1 - Usa los brazos o el movimiento es un poco brusco</option>
                                        <option value="2">2 - Seguro, movimiento suave y controlado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Marcha (Gait) --}}
                        <div>
                            <h5 style="margin:0 0 12px; color:var(--accent-primary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:4px; font-size:14px; font-weight:600;">Sección 2: Marcha (Máx. 12)</h5>
                            <div style="display:flex; flex-direction:column; gap:10px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">1. Iniciación de la Marcha</label>
                                    <select wire:model="tinetti_gait.initiation" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Dudas o múltiples intentos para comenzar</option>
                                        <option value="1">1 - Sin dudas ni vacilaciones</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">2. Longitud del Paso</label>
                                    <select wire:model="tinetti_gait.step_length" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - El pie no sobrepasa al contralateral</option>
                                        <option value="1">1 - El pie sobrepasa ligeramente</option>
                                        <option value="2">2 - El pie sobrepasa con longitud normal</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">3. Altura del Paso</label>
                                    <select wire:model="tinetti_gait.step_height" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Arrastra el pie o elevación mínima</option>
                                        <option value="1">1 - Elevación reducida pero no arrastra</option>
                                        <option value="2">2 - Elevación normal, despega el pie completamente</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">4. Simetría del Paso</label>
                                    <select wire:model="tinetti_gait.step_symmetry" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - La longitud de los pasos no es igual</option>
                                        <option value="1">1 - La longitud de los pasos es igual</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">5. Fluidez del Paso (Continuidad)</label>
                                    <select wire:model="tinetti_gait.step_continuity" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Paradas o discontinuidad entre pasos</option>
                                        <option value="1">1 - Pasos continuos y fluidos</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">6. Trayectoria (Desviación)</label>
                                    <select wire:model="tinetti_gait.path" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Desviación marcada de la línea recta</option>
                                        <option value="1">1 - Desviación leve o usa ayuda para mantener línea</option>
                                        <option value="2">2 - Camina en línea recta sin ayuda ni desviación</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">7. Tronco</label>
                                    <select wire:model="tinetti_gait.trunk" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Balanceo marcado o requiere soporte continuo</option>
                                        <option value="1">1 - No se balancea pero flexiona tronco/rodillas o separa brazos</option>
                                        <option value="2">2 - Tronco erguido, marcha estable y controlada</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">8. Postura al Caminar</label>
                                    <select wire:model="tinetti_gait.walking_stance" class="form-control-modern" style="font-size:12px; padding:4px 8px;">
                                        <option value="0">0 - Talones separados (base ancha)</option>
                                        <option value="1">1 - Talones casi se tocan al caminar (base normal)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-16">
                        <label class="form-label">Notas Adicionales (Tinetti)</label>
                        <textarea wire:model="tinetti_notes" class="form-control-modern" rows="2" placeholder="Observaciones sobre el equilibrio o la marcha..."></textarea>
                    </div>

                    <div style="font-size:16px; font-weight:700; margin:16px 0; color:var(--accent-primary); text-align:right;">
                        Puntuación Estimada: {{ array_sum($tinetti_balance) + array_sum($tinetti_gait) }} / 28
                        <span style="font-size:12px; display:block; color:var(--text-secondary); font-weight:normal; margin-top:2px;">
                            (Equilibrio: {{ array_sum($tinetti_balance) }}/16, Marcha: {{ array_sum($tinetti_gait) }}/12)
                        </span>
                    </div>
                @endif

                {{-- Borg / 6MWT Layout --}}
                @if ($scaleType === 'borg_6mwt')
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:20px;">
                        <div>
                            <h5 style="margin:0 0 12px; color:var(--accent-primary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:4px; font-size:14px; font-weight:600;">Esfuerzo Percibido (Escala de Borg 0-10)</h5>
                            <div class="grid grid-2" style="gap:12px;">
                                <div class="form-group">
                                    <label class="form-label">Borg Inicio</label>
                                    <input type="number" wire:model="borg_initial" class="form-control-modern" min="0" max="10" step="0.5">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Borg Final</label>
                                    <input type="number" wire:model="borg_final" class="form-control-modern" min="0" max="10" step="0.5">
                                </div>
                            </div>

                            <div class="form-group mb-16">
                                <label class="form-label">Distancia Recorrida (Metros)</label>
                                <input type="number" wire:model="distance_meters" class="form-control-modern" min="0" placeholder="Ej. 350">
                            </div>
                        </div>

                        <div>
                            <h5 style="margin:0 0 12px; color:var(--accent-primary); border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:4px; font-size:14px; font-weight:600;">Signos Vitales Relacionados</h5>
                            <div class="grid grid-2" style="gap:10px;">
                                <div class="form-group">
                                    <label class="form-label">F.C. Inicio (lpm)</label>
                                    <input type="text" wire:model="vital_signs.hr_start" class="form-control-modern" placeholder="Ej. 72" style="padding:4px 8px; font-size:12px;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">F.C. Final (lpm)</label>
                                    <input type="text" wire:model="vital_signs.hr_end" class="form-control-modern" placeholder="Ej. 110" style="padding:4px 8px; font-size:12px;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SpO₂ Inicio (%)</label>
                                    <input type="text" wire:model="vital_signs.spo2_start" class="form-control-modern" placeholder="Ej. 98" style="padding:4px 8px; font-size:12px;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">SpO₂ Final (%)</label>
                                    <input type="text" wire:model="vital_signs.spo2_end" class="form-control-modern" placeholder="Ej. 95" style="padding:4px 8px; font-size:12px;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">T.A. Inicio (mmHg)</label>
                                    <input type="text" wire:model="vital_signs.bp_start" class="form-control-modern" placeholder="Ej. 120/80" style="padding:4px 8px; font-size:12px;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">T.A. Final (mmHg)</label>
                                    <input type="text" wire:model="vital_signs.bp_end" class="form-control-modern" placeholder="Ej. 135/85" style="padding:4px 8px; font-size:12px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-16">
                        <label class="form-label">Notas Adicionales (Borg / 6MWT)</label>
                        <textarea wire:model="borg_notes" class="form-control-modern" rows="2" placeholder="Observaciones de la prueba o de los signos vitales..."></textarea>
                    </div>
                @endif

                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" wire:click="toggleForm" class="btn-modern btn-secondary btn-sm">Cancelar</button>
                    <button type="submit" class="btn-modern btn-primary btn-sm"><i class="fas fa-save"></i> Guardar Evaluación</button>
                </div>
            </form>
        </div>
    @endif

    {{-- List --}}
    <div style="display:flex; flex-direction:column; gap:16px;">
        @forelse($scales as $scale)
            <div class="glass-card" style="padding:20px; border:1px solid rgba(255,255,255,0.05); position:relative;">
                <div style="position:absolute; top:20px; right:20px; display:flex; gap:8px;">
                    <button type="button" wire:click="editScale({{ $scale->id }})" class="btn-modern btn-secondary btn-sm" style="padding:4px 8px; font-size:11px;">
                        <i class="fas fa-pen"></i> Editar
                    </button>
                    <button type="button" onclick="if(confirm('¿Seguro que deseas eliminar esta evaluación?')) { @this.deleteScale({{ $scale->id }}) }" class="btn-modern btn-danger btn-sm" style="padding:4px 8px; font-size:11px;">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>

                @if ($scale->type === 'tinetti')
                    <div style="margin-bottom:8px;">
                        <span style="font-size:11px; background:rgba(59, 130, 246, 0.15); color:#3b82f6; padding:2px 8px; border-radius:12px; font-weight:600;">Escala de Tinetti</span>
                        <span style="font-size:12px; color:var(--text-secondary); margin-left:8px;"><i class="fas fa-calendar-alt"></i> {{ $scale->date->format('d/m/Y') }}</span>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin:16px 0;">
                        <div>
                            <strong style="font-size:11px; color:var(--text-muted); display:block; text-transform:uppercase;">Puntuación Total</strong>
                            <span style="font-size:24px; font-weight:800; color:var(--accent-primary);">{{ $scale->data['total_score'] ?? 0 }} <span style="font-size:14px; font-weight:normal; color:var(--text-secondary);">/ 28</span></span>
                        </div>
                        <div>
                            <strong style="font-size:11px; color:var(--text-muted); display:block; text-transform:uppercase;">Equilibrio</strong>
                            <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $scale->data['balance_score'] ?? 0 }} / 16</span>
                        </div>
                        <div>
                            <strong style="font-size:11px; color:var(--text-muted); display:block; text-transform:uppercase;">Marcha</strong>
                            <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $scale->data['gait_score'] ?? 0 }} / 12</span>
                        </div>
                    </div>

                    @if(!empty($scale->data['notes']))
                        <div style="font-size:13px; color:var(--text-secondary); border-top:1px solid rgba(255,255,255,0.05); padding-top:8px;">
                            <strong>Notas:</strong> {{ $scale->data['notes'] }}
                        </div>
                    @endif

                @else
                    {{-- Borg / 6MWT display --}}
                    <div style="margin-bottom:8px;">
                        <span style="font-size:11px; background:rgba(16, 185, 129, 0.15); color:#10b981; padding:2px 8px; border-radius:12px; font-weight:600;">Escala de Borg y Caminata 6 Min</span>
                        <span style="font-size:12px; color:var(--text-secondary); margin-left:8px;"><i class="fas fa-calendar-alt"></i> {{ $scale->date->format('d/m/Y') }}</span>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; margin:16px 0;">
                        <div>
                            <strong style="font-size:11px; color:var(--text-muted); display:block; text-transform:uppercase;">Distancia Recorrida</strong>
                            <span style="font-size:24px; font-weight:800; color:var(--accent-primary);">{{ $scale->data['distance_meters'] ?? 0 }} <span style="font-size:14px; font-weight:normal; color:var(--text-secondary);">metros</span></span>
                        </div>
                        <div>
                            <strong style="font-size:11px; color:var(--text-muted); display:block; text-transform:uppercase;">Esfuerzo Borg (Inicio -> Fin)</strong>
                            <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $scale->data['borg_initial'] ?? 0 }} &rarr; {{ $scale->data['borg_final'] ?? 0 }} <span style="font-size:11px; color:var(--text-muted); font-weight:normal;">/ 10</span></span>
                        </div>
                    </div>

                    {{-- Vital Signs Grid --}}
                    @if(!empty($scale->data['vital_signs']))
                        @php $vs = $scale->data['vital_signs']; @endphp
                        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; padding:10px; background:rgba(255,255,255,0.02); border-radius:6px; margin-bottom:12px;">
                            <div>
                                <span style="font-size:11px; color:var(--text-muted); display:block;">F. Cardiaco (Inicio -> Fin)</span>
                                <span style="font-size:12px; color:var(--text-primary); font-weight:600;">{{ $vs['hr_start'] ?: '--' }} lpm &rarr; {{ $vs['hr_end'] ?: '--' }} lpm</span>
                            </div>
                            <div>
                                <span style="font-size:11px; color:var(--text-muted); display:block;">Saturación SpO₂ (Inicio -> Fin)</span>
                                <span style="font-size:12px; color:var(--text-primary); font-weight:600;">{{ $vs['spo2_start'] ?: '--' }}% &rarr; {{ $vs['spo2_end'] ?: '--' }}%</span>
                            </div>
                            <div>
                                <span style="font-size:11px; color:var(--text-muted); display:block;">Tensión Arterial (Inicio -> Fin)</span>
                                <span style="font-size:12px; color:var(--text-primary); font-weight:600;">{{ $vs['bp_start'] ?: '--' }} &rarr; {{ $vs['bp_end'] ?: '--' }} mmHg</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($scale->data['notes']))
                        <div style="font-size:13px; color:var(--text-secondary); border-top:1px solid rgba(255,255,255,0.05); padding-top:8px;">
                            <strong>Notas:</strong> {{ $scale->data['notes'] }}
                        </div>
                    @endif
                @endif
            </div>
        @empty
            <div style="background:rgba(255,255,255,0.02); border:1px dashed rgba(255,255,255,0.1); padding:40px; border-radius:8px; text-align:center; color:var(--text-muted);">
                <i class="fas fa-tasks" style="font-size:32px; display:block; margin-bottom:12px; color:var(--text-muted);"></i>
                No se han registrado evaluaciones ni escalas clínicas en esta consulta.
            </div>
        @endforelse
    </div>
</div>

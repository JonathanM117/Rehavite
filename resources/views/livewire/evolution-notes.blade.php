<div>
    <div class="section-header" wire:key="evolution-notes-main-header">
        <i class="fas fa-sticky-note"></i>
        <h3>Notas de Evolución</h3>
        <div style="margin-left:auto;">
            <button class="btn-modern btn-primary btn-sm" 
                    type="button"
                    wire:click="toggleForm"
                    style="justify-content:center; text-align:center;">
                <i class="fas fa-plus"></i> Nueva Nota
            </button>
        </div>
    </div>

    {{-- ═══ FORM ═══ --}}
    <div wire:key="evolution-note-form-wrapper">
    @if($showForm)
        <div id="evolution-note-modal"
             x-data="{}"
             x-init="document.body.style.overflow = 'hidden'; return () => { document.body.style.overflow = ''; }"
             onclick="if(event.target===this) handleUnsavedExit('evolution-note-form', () => { @this.cancelEdit(); })"
             style="position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); padding:24px; overflow-y:auto; display:flex; align-items:flex-start; justify-content:center;">
             
             <div style="background:var(--bg-app); max-width:800px; width:100%; margin:40px auto; border-radius:16px; padding:32px; position:relative; box-shadow:0 30px 80px rgba(0,0,0,0.6);">
                 
                 {{-- Modal Header --}}
                 <div class="flex-between" style="margin-bottom:24px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:16px;">
                     <h2 style="margin:0; font-size:20px; font-weight:700;"><i class="fas fa-pen"></i> {{ $editingNoteId ? 'Editar Nota de Evolución' : 'Nueva Nota de Evolución' }}</h2>
                     <button type="button" 
                             onclick="handleUnsavedExit('evolution-note-form', () => { @this.cancelEdit(); })"
                             style="background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); cursor:pointer;">
                         <i class="fas fa-times"></i>
                     </button>
                 </div>

                 <form id="evolution-note-form" wire:submit.prevent="saveNote"
                       oninput="this.dataset.dirty = 'true'"
                       onchange="this.dataset.dirty = 'true'"
                       onsubmit="delete this.dataset.dirty; var b = this.querySelector('button[type=submit]'); if(b) { b.disabled = true; b.innerHTML = '<i class=\'fas fa-spinner fa-spin\'></i> Guardando...'; }">

            <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Fecha *</label>
                <input type="date" class="form-control-modern" wire:model="date" max="{{ date('Y-m-d') }}">
                @error('date') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">N° Sesión</label>
                <input type="number" class="form-control-modern" wire:model="session_number" min="1">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea class="form-control-modern" wire:model="description" rows="3" spellcheck="true" placeholder="¿Cómo llegó el paciente? ¿Cómo ha sentido el tratamiento? ¿Cómo ha evolucionado?"></textarea>
        </div>

        {{-- Vital Signs --}}
        <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:12px;margin-top:8px;">
            Signos Vitales
        </div>
        <div class="grid grid-2" style="gap:12px;">
            <div class="form-group">
                <label class="form-label">Temperatura Inicio (°C)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.temp_start" placeholder="36.5">
            </div>
            <div class="form-group">
                <label class="form-label">Temperatura Final (°C)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.temp_end" placeholder="36.5">
            </div>
            <div class="form-group">
                <label class="form-label">Saturación O₂ Inicio (%)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.saturation_start" placeholder="98">
            </div>
            <div class="form-group">
                <label class="form-label">Saturación O₂ Final (%)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.saturation_end" placeholder="98">
            </div>
            <div class="form-group">
                <label class="form-label">T.A. Inicio (mmHg)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.bp_start" placeholder="120/80">
            </div>
            <div class="form-group">
                <label class="form-label">T.A. Final (mmHg)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.bp_end" placeholder="120/80">
            </div>
            <div class="form-group">
                <label class="form-label">F.C. Inicio (lpm)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.hr_start" placeholder="72">
            </div>
            <div class="form-group">
                <label class="form-label">F.C. Final (lpm)</label>
                <input type="text" class="form-control-modern" wire:model="vital_signs.hr_end" placeholder="72">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Plan</label>
            <textarea class="form-control-modern" wire:model="plan" rows="2" spellcheck="true" placeholder="¿Cómo va a continuar el tratamiento? ¿Se deben hacer ajustes?"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tratamiento</label>
            <textarea class="form-control-modern" wire:model="treatment" rows="2" spellcheck="true" placeholder="¿Qué se realizó durante la sesión?"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Notas Adicionales</label>
            <textarea class="form-control-modern" wire:model="additional_notes" rows="2" spellcheck="true"></textarea>
        </div>
            <div class="form-group">
                <label class="form-label">Comentarios</label>
                <textarea class="form-control-modern" wire:model="comments" rows="2" spellcheck="true" placeholder="Comentarios finales u observaciones..."></textarea>
            </div>

            <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;">
                <button type="button" class="btn-modern btn-secondary btn-sm"
                        onclick="handleUnsavedExit('evolution-note-form', () => { @this.cancelEdit(); })"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex:1; max-width:200px; text-align:center;">
                    Cancelar
                </button>
                <button type="submit" id="save-evolution-note-btn" class="btn-modern btn-primary btn-sm" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex:1; max-width:200px; text-align:center;" wire:loading.attr="disabled" wire:target="saveNote">
                    <i class="fas fa-save" wire:loading.remove wire:target="saveNote"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveNote"></i>
                    <span>{{ $editingNoteId ? 'Actualizar Nota' : 'Guardar Nota' }}</span>
                </button>
            </div>
        </form>
             </div>
        </div>
    @endif
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

    {{-- ═══ NOTES LIST ═══ --}}
    <div wire:key="notes-list-wrapper" x-data="{
        highlightNote(id) {
            setTimeout(() => {
                let el = document.getElementById('note-item-' + id);
                if(el) {
                    el.scrollIntoView({behavior: 'smooth', block: 'center'});
                    el.style.transition = 'all 0.4s ease';
                    let oldBg = el.style.backgroundColor;
                    let oldBorder = el.style.borderColor;
                    let oldShadow = el.style.boxShadow;
                    
                    el.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
                    el.style.borderColor = 'var(--accent-primary)';
                    el.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.3)';
                    
                    setTimeout(() => {
                        el.style.backgroundColor = oldBg;
                        el.style.borderColor = oldBorder;
                        el.style.boxShadow = oldShadow;
                        setTimeout(() => { el.style.transition = ''; }, 400);
                    }, 2000);
                }
            }, 100);
        }
    }" x-on:note-saved.window="highlightNote($event.detail.id)">
    @forelse ($notes as $note)
        <div class="note-card" id="note-item-{{ $note->id }}" wire:key="note-{{ $note->id }}">
            <div class="flex-between mb-8">
                <div>
                    <span style="font-weight:600;color:var(--accent-primary);">
                        {{ $note->date ? $note->date->format('d/m/Y') : '—' }}
                    </span>
                    @if($note->session_number)
                        <span class="badge-modern badge-active" style="margin-left:8px;">Sesión {{ $note->session_number }}</span>
                    @endif
                    @if($note->user)
                        <span style="margin-left:8px; font-size:11px; color:var(--text-muted);"><i class="fas fa-user-md"></i> {{ $note->user->name }}</span>
                    @endif
                </div>
                <div class="flex-center gap-8">
                    <button class="btn-icon" wire:click="editNote({{ $note->id }})" title="Editar">
                        <i class="fas fa-pen" style="font-size:12px;"></i>
                    </button>
                    <button class="btn-icon" wire:click="deleteNote({{ $note->id }})" title="Eliminar"
                            onclick="return confirm('¿Eliminar esta nota?')"
                            style="color:var(--danger);">
                        <i class="fas fa-trash" style="font-size:12px;"></i>
                    </button>
                </div>
            </div>

            @if($note->description)
                <p style="font-size:14px;margin:8px 0;line-height:1.5;">{{ $note->description }}</p>
            @endif

            {{-- Vital signs summary --}}
            @if($note->vital_signs && array_filter($note->vital_signs))
                @php
                    $vitalLabels = [
                        'temp_start'       => 'Temp. Inicio',
                        'temp_end'         => 'Temp. Fin',
                        'saturation_start' => 'Sat. O₂ Inicio',
                        'saturation_end'   => 'Sat. O₂ Fin',
                        'bp_start'         => 'T.A. Inicio',
                        'bp_end'           => 'T.A. Fin',
                        'hr_start'         => 'F.C. Inicio',
                        'hr_end'           => 'F.C. Fin',
                    ];
                @endphp
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:10px;">
                    @foreach($note->vital_signs as $key => $value)
                        @if($value)
                            <span wire:key="vital-{{ $note->id }}-{{ $key }}" style="font-size:12px;color:var(--text-muted);background:var(--bg-glass);padding:3px 8px;border-radius:4px;">
                                {{ $vitalLabels[$key] ?? str_replace('_', ' ', ucfirst($key)) }}: <strong style="color:var(--text-secondary);">{{ $value }}</strong>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($note->plan || $note->treatment || $note->additional_notes || $note->comments)
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(255,255,255,0.05);font-size:13px;">
                    @if($note->plan)
                        <div style="color:var(--text-secondary);"><strong>Plan:</strong> {{ $note->plan }}</div>
                    @endif
                    @if($note->treatment)
                        <div style="color:var(--text-secondary);margin-top:4px;"><strong>Tratamiento:</strong> {{ $note->treatment }}</div>
                    @endif
                    @if($note->additional_notes)
                        <div style="color:var(--text-secondary);margin-top:4px;"><strong>Notas Adicionales:</strong> {{ $note->additional_notes }}</div>
                    @endif
                    @if($note->comments)
                        <div style="color:var(--text-secondary);margin-top:4px;"><strong>Comentarios:</strong> {{ $note->comments }}</div>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state" style="padding:24px;">
            <i class="fas fa-sticky-note" style="font-size:32px;"></i>
            <p>No hay notas de evolución registradas</p>
        </div>
    @endforelse
    </div>
</div>

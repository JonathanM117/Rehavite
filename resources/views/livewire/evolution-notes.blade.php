<div>
    <div class="section-header" wire:key="evolution-notes-main-header">
        <i class="fas fa-sticky-note"></i>
        <h3>Notas de Evolución</h3>
        <div style="margin-left:auto;">
            <button class="btn-modern btn-primary btn-sm" wire:click="toggleForm">
                <i class="fas fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                {{ $showForm ? 'Cancelar' : 'Nueva Nota' }}
            </button>
        </div>
    </div>

    {{-- ═══ FORM ═══ --}}
    <div wire:key="evolution-note-form-wrapper">
    @if($showForm)
    <div class="glass-card mb-24" style="border-color:var(--border-accent);">
        <div style="font-weight:600;margin-bottom:16px;font-size:14px;">
            {{ $editingNoteId ? 'Editar Nota' : 'Nueva Nota de Evolución' }}
        </div>

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
            <textarea class="form-control-modern" wire:model="description" rows="3"></textarea>
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
            <textarea class="form-control-modern" wire:model="plan" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Tratamiento</label>
            <textarea class="form-control-modern" wire:model="treatment" rows="2"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Notas Adicionales</label>
            <textarea class="form-control-modern" wire:model="additional_notes" rows="2"></textarea>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button class="btn-modern btn-secondary btn-sm" wire:click="cancelEdit">Cancelar</button>
            <button class="btn-modern btn-primary btn-sm" wire:click="saveNote">
                <i class="fas fa-save"></i> {{ $editingNoteId ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
    </div>
    @endif
    </div>

    {{-- ═══ NOTES LIST ═══ --}}
    <div wire:key="notes-list-wrapper">
    @forelse ($notes as $note)
        <div class="note-card" wire:key="note-{{ $note->id }}">
            <div class="flex-between mb-8">
                <div>
                    <span style="font-weight:600;color:var(--accent-primary);">
                        {{ $note->date ? $note->date->format('d/m/Y') : '—' }}
                    </span>
                    @if($note->session_number)
                        <span class="badge-modern badge-active" style="margin-left:8px;">Sesión {{ $note->session_number }}</span>
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
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:10px;">
                    @foreach($note->vital_signs as $key => $value)
                        @if($value)
                            <span wire:key="vital-{{ $note->id }}-{{ $key }}" style="font-size:12px;color:var(--text-muted);background:var(--bg-glass);padding:3px 8px;border-radius:4px;">
                                {{ str_replace('_', ' ', ucfirst($key)) }}: <strong style="color:var(--text-secondary);">{{ $value }}</strong>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($note->plan || $note->treatment || $note->additional_notes)
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

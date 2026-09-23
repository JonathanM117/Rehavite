<div>
    @if($saved)
        <div x-data x-init="setTimeout(() => { $wire.set('saved', false) }, 3000)"
             style="background:rgba(39,174,96,0.1); border:1px solid rgba(39,174,96,0.3); color:var(--success); padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-check-circle"></i> Configuraciones guardadas correctamente.
        </div>
    @endif

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; max-width:600px;">

        <div class="glass-card">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                <i class="fas fa-trash" style="color:var(--danger);"></i> Papelera
            </div>
            <label class="form-label">Días antes de eliminación automática</label>
            <div style="display:flex; align-items:center; gap:12px; margin-top:8px;">
                <input type="number" wire:model.lazy="trash_days" min="1" max="365"
                       class="form-control-modern" style="max-width:100px;">
                <span style="font-size:13px; color:var(--text-muted);">días</span>
            </div>
            @error('trash_days') <span style="color:var(--danger); font-size:12px;">{{ $message }}</span> @enderror
            <p style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                Los pacientes en la papelera se eliminan permanentemente después de este período.
            </p>
        </div>

        <div class="glass-card">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                <i class="fas fa-history" style="color:#3b82f6;"></i> Historial
            </div>
            <label class="form-label">Ventana de agrupación de acciones</label>
            <div style="display:flex; align-items:center; gap:12px; margin-top:8px;">
                <input type="number" wire:model.lazy="audit_group_minutes" min="1" max="120"
                       class="form-control-modern" style="max-width:100px;">
                <span style="font-size:13px; color:var(--text-muted);">minutos</span>
            </div>
            @error('audit_group_minutes') <span style="color:var(--danger); font-size:12px;">{{ $message }}</span> @enderror
            <p style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                Las acciones repetidas del mismo tipo en este período se agrupan en el historial.
            </p>
        </div>

        <div class="glass-card" style="grid-column: 1 / -1; opacity:0.5;">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                <i class="fas fa-database" style="color:#f59e0b;"></i> Respaldo de Información
                <span style="font-size:11px; background:rgba(245,158,11,0.1); color:#f59e0b; padding:2px 8px; border-radius:12px;">Próximamente</span>
            </div>
            <p style="font-size:13px; color:var(--text-muted); margin:0;">
                Configura el día y hora para realizar respaldos automáticos de la base de datos.
            </p>
        </div>
    </div>

    <div style="margin-top:24px;">
        <button class="btn-modern btn-primary" wire:click="save" wire:loading.attr="disabled">
            <i class="fas fa-save" wire:loading.remove wire:target="save"></i>
            <i class="fas fa-spinner fa-spin" wire:loading wire:target="save"></i>
            Guardar Configuraciones
        </button>
    </div>
</div>

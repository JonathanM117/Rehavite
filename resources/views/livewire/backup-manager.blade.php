<div class="glass-card" style="padding: 24px;">
    <div class="flex-between mb-24" style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 16px;">
        <div>
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text-primary);">Copias de Seguridad (Base de Datos)</h3>
            <p style="margin: 4px 0 0; font-size: 12px; color: var(--text-secondary);">Genera archivos de respaldo comprimidos y encriptados con AES-256 para recuperación ante desastres.</p>
        </div>
        <button type="button" wire:click="runBackup" class="btn-modern btn-primary btn-sm">
            <i class="fas fa-file-export"></i> Generar Respaldo Ahora
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert-modern alert-success mb-24" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); padding: 12px; border-radius: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert-modern alert-danger mb-24" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); padding: 12px; border-radius: 8px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 16px; display: flex; gap: 12px; align-items: flex-start; margin-bottom: 24px;">
        <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px; margin-top: 2px;"></i>
        <div>
            <strong style="color: #f59e0b; font-size: 14px; display: block; margin-bottom: 4px;">Advertencia sobre la Restauración</strong>
            <span style="font-size: 12px; color: var(--text-secondary); line-height: 1.5; display: block;">
                Restaurar un respaldo reemplazará de forma completa e irreversible todos los datos actuales del sistema por los contenidos en la copia de seguridad. Asegúrese de que no haya otros usuarios activos operando en el sistema antes de proceder.
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-modern" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Archivo</th>
                    <th style="text-align: left;">Tamaño</th>
                    <th style="text-align: left;">Fecha de Creación</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $b)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 12px 8px; font-weight: 600; color: var(--text-primary); font-family: monospace; font-size: 12px;">
                            {{ $b['name'] }}
                        </td>
                        <td style="padding: 12px 8px; color: var(--text-primary); font-size: 13px;">
                            {{ number_format($b['size'] / 1024, 2) }} KB
                        </td>
                        <td style="padding: 12px 8px; color: var(--text-primary); font-size: 13px;">
                            {{ date('d/m/Y H:i:s', $b['created_at']) }}
                        </td>
                        <td style="padding: 12px 8px; text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <button type="button" 
                                        onclick="if(confirm('¿Seguro que deseas restaurar este respaldo? Reemplazará permanentemente los datos actuales.')) { @this.restore('{{ $b['name'] }}') }" 
                                        class="btn-modern btn-success btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                    <i class="fas fa-undo"></i> Restaurar
                                </button>
                                <button type="button" 
                                        onclick="if(confirm('¿Seguro que deseas eliminar este archivo de respaldo?')) { @this.delete('{{ $b['name'] }}') }" 
                                        class="btn-modern btn-danger btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 13px;">
                            <i class="fas fa-database" style="font-size: 28px; display: block; margin-bottom: 12px; color: var(--text-muted);"></i>
                            No se han generado copias de seguridad aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

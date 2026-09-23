<div class="glass-card" style="padding: 24px;">
    <div class="mb-24" style="border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 16px;">
        <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text-primary);">Banco de Ejercicios</h3>
        <p style="margin: 4px 0 0; font-size: 12px; color: var(--text-secondary);">
            Sube el archivo HTML del buscador de ejercicios. Se mostrará automáticamente a todos los pacientes desde el botón "Plan de ejercicios" en su expediente, sin necesidad de tocar el código.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="alert-modern alert-success mb-24" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); padding: 12px; border-radius: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 16px; display: flex; gap: 12px; align-items: flex-start; margin-bottom: 24px;">
        <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px; margin-top: 2px;"></i>
        <div>
            <strong style="color: #f59e0b; font-size: 14px; display: block; margin-bottom: 4px;">Antes de subir</strong>
            <span style="font-size: 12px; color: var(--text-secondary); line-height: 1.5; display: block;">
                El archivo reemplaza inmediatamente la versión actual para todos los pacientes. Debe ser el mismo archivo HTML autocontenido que exporta la herramienta (con estilos, ejercicios e imágenes incluidos).
            </span>
        </div>
    </div>

    <div class="mb-24">
        <h4 style="margin: 0 0 12px; font-size: 13px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px;">Archivo actual</h4>

        @if ($currentFile)
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:14px; background: rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:8px;">
                <div>
                    <div style="font-weight:600; color:var(--text-primary); font-size:13px;">
                        <i class="fas fa-file-code" style="color:#0E6E6E; margin-right:6px;"></i>
                        {{ $currentFile['original_name'] }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                        {{ number_format($currentFile['size'] / 1024 / 1024, 2) }} MB ·
                        Subido el {{ \Carbon\Carbon::parse($currentFile['uploaded_at'])->format('d/m/Y H:i') }}
                        @if($currentFile['uploaded_by']) por {{ $currentFile['uploaded_by'] }} @endif
                    </div>
                </div>
                <a href="{{ route('admin.exercise-bank.preview') }}" target="_blank" class="btn-modern btn-secondary btn-sm">
                    <i class="fas fa-eye"></i> Ver / Probar
                </a>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-file-code"></i>
                <p>Aún no se ha subido ningún archivo. Los pacientes verán un aviso hasta que subas uno.</p>
            </div>
        @endif
    </div>

    <form wire:submit.prevent="save">
        <div class="form-group"
             x-data="{ uploading: false, progress: 0 }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false; alert('Error al subir el archivo. Asegúrate de que sea un archivo .html y que no supere los 20MB.')"
             x-on:livewire-upload-progress="progress = $event.detail.progress">
            <label class="form-label">{{ $currentFile ? 'Reemplazar archivo' : 'Subir archivo' }}</label>
            <input type="file" wire:model="file" class="form-control-modern" accept=".html,.htm">
            <div x-show="uploading" style="margin-top: 10px; width: 100%; background-color: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; height: 6px;">
                <div style="height: 100%; background-color: var(--accent-primary); transition: width 0.3s;" x-bind:style="'width: ' + progress + '%'"></div>
            </div>
            <small style="color:var(--text-muted); font-size:11px; margin-top:4px; display:block;">Solo se permiten archivos .html (máx. 20MB)</small>
            @error('file') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div style="margin-top:16px;">
            <button type="submit" class="btn-modern btn-primary btn-sm" wire:loading.attr="disabled" wire:target="file, save">
                <i class="fas fa-upload" wire:loading.remove wire:target="save"></i>
                <i class="fas fa-spinner fa-spin" wire:loading wire:target="save"></i>
                {{ $currentFile ? 'Reemplazar' : 'Subir' }} archivo
            </button>
        </div>
    </form>
</div>

<div>
    <div class="section-header" wire:key="studies-main-header">
        <i class="fas fa-image"></i>
        <h3>Estudios Médicos</h3>
        <div style="margin-left:auto;">
            <button class="btn-modern btn-primary btn-sm" wire:click="toggleForm">
                <i class="fas fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                {{ $showForm ? 'Cancelar' : 'Subir Estudio' }}
            </button>
        </div>
    </div>

    @if($showForm)
    <div class="glass-card mb-24" wire:key="study-form-wrapper" style="border: 1px dashed var(--accent-primary);">
        <form wire:submit.prevent="saveStudy">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Fecha del Estudio *</label>
                    <input type="date" wire:model="date" class="form-control-modern" max="{{ date('Y-m-d') }}" required>
                    @error('date') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Estudio *</label>
                    <select wire:model="type" class="form-control-modern" required>
                        <option value="radiografia">Radiografía</option>
                        <option value="resonancia">Resonancia Magnética</option>
                        <option value="tomografia">Tomografía (TAC)</option>
                        <option value="ultrasonido">Ultrasonido</option>
                        <option value="laboratorio">Análisis de Laboratorio</option>
                        <option value="otro">Otro</option>
                    </select>
                    @error('type') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción / Hallazgos</label>
                <textarea wire:model="description" class="form-control-modern" rows="2" placeholder="Breve descripción del estudio..."></textarea>
                @error('description') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group"
                 x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false; alert('Error al subir el archivo. Asegúrate de que sea una imagen (JPG, PNG, WEBP) o un PDF, y que no supere los 10MB.')"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <label class="form-label">Archivo (Imagen o PDF) *</label>
                <div class="file-upload-wrapper">
                    <input type="file" wire:model="file" class="form-control-modern" id="study-file-input">
                    <div wire:loading wire:target="file" style="margin-top:8px; font-size:12px; color:var(--accent-primary);">
                        <i class="fas fa-spinner fa-spin"></i> Subiendo archivo...
                    </div>
                </div>
                @error('file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
                <button type="submit" class="btn-modern btn-primary" wire:loading.attr="disabled" wire:target="file, saveStudy">
                    <i class="fas fa-save" wire:loading.remove wire:target="saveStudy"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveStudy"></i>
                    Guardar Estudio
                </button>
            </div>
        </form>
    </div>
    @endif

    <div wire:key="studies-list-wrapper">
        @if(session()->has('success'))
            <div class="alert-modern alert-success mb-16">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(count($studies) > 0)
            <div class="grid grid-2">
                @foreach($studies as $study)
                    <div class="glass-card" wire:key="study-item-{{ $study->id }}" style="padding:12px; height: 100%; display: flex; flex-direction: column;">
                        <div class="flex-between mb-8">
                            <span class="badge-modern badge-info" style="font-size:10px;">{{ strtoupper($study->type) }}</span>
                            <span style="font-size:11px;color:var(--text-muted);">{{ $study->date->format('d/m/Y') }}</span>
                        </div>
                        
                        <div style="flex-grow:1;">
                            @if(Str::endsWith($study->file_path, ['.jpg', '.jpeg', '.png', '.webp']))
                                <div style="width:100%; height:120px; border-radius:8px; overflow:hidden; background:#000; margin-bottom:10px;">
                                    <img src="{{ Storage::url($study->file_path) }}" style="width:100%; height:100%; object-fit: cover; opacity:0.8; transition:opacity 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
                                </div>
                            @else
                                <div style="width:100%; height:120px; border-radius:8px; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; margin-bottom:10px; border:1px solid rgba(255,255,255,0.1);">
                                    <i class="fas fa-file-pdf" style="font-size:40px; color:var(--danger);"></i>
                                </div>
                            @endif

                            @if($study->description)
                                <p style="font-size:12px; color:var(--text-secondary); margin:0 0 12px 0; line-height:1.4;">{{ Str::limit($study->description, 60) }}</p>
                            @endif
                        </div>

                        <div class="flex-between" style="margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                            @if(auth()->user()->admin)
                            <a href="{{ Storage::url($study->file_path) }}" download class="btn-modern btn-primary btn-sm" style="flex:1; justify-content:center;">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                            @endif
                            <a href="{{ Storage::url($study->file_path) }}#toolbar=0" target="_blank" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center; margin-left:{{ auth()->user()->admin ? '8px' : '0' }};">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <button wire:click="deleteStudy({{ $study->id }})" wire:confirm="¿Seguro que deseas eliminar este estudio?" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); margin-left:8px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-image"></i>
                <p>No hay estudios registrados para esta consulta</p>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('alert', event => {
            alert(event.detail.message || event.detail[0].message);
        });
    </script>
</div>

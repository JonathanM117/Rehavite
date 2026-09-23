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
        <div style="font-weight:600;margin-bottom:16px;font-size:14px;">
            {{ $editingStudyId ? 'Editar Estudio Médico' : 'Subir Nuevo Estudio' }}
        </div>
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
                <label class="form-label">Archivo (Imagen o PDF) {{ $editingStudyId ? '(Dejar vacío para conservar el actual)' : '*' }}</label>
                <div class="file-upload-wrapper">
                    <input type="file" wire:model="file" class="form-control-modern" id="study-file-input">
                    <div wire:loading wire:target="file" style="margin-top:8px; font-size:12px; color:var(--accent-primary);">
                        <i class="fas fa-spinner fa-spin"></i> Subiendo archivo...
                    </div>
                </div>
                <!-- Barra de Progreso Alpine JS -->
                <div x-show="uploading" style="margin-top: 10px; width: 100%; background-color: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; height: 6px;">
                    <div style="height: 100%; background-color: var(--accent-primary); transition: width 0.3s;" x-bind:style="'width: ' + progress + '%'"></div>
                </div>
                @error('file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;">
                <button type="button" class="btn-modern btn-secondary btn-sm" wire:click="cancelEdit" style="flex:1; max-width:200px; text-align:center; justify-content:center;">Cancelar</button>
                <button type="submit" class="btn-modern btn-primary btn-sm" x-bind:disabled="uploading" wire:loading.attr="disabled" wire:target="file, saveStudy" style="flex:1; max-width:200px; text-align:center; justify-content:center;" x-bind:style="uploading ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                    <i class="fas fa-save" wire:loading.remove wire:target="saveStudy"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveStudy"></i>
                    {{ $editingStudyId ? 'Actualizar Estudio' : 'Guardar Estudio' }}
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
                            @php
                                $filePathLower = strtolower($study->file_path);
                                $isPdf = Str::endsWith($filePathLower, '.pdf');
                            @endphp
                            @if(!$isPdf)
                                <div style="width:100%; height:120px; border-radius:8px; overflow:hidden; background:rgba(0,0,0,0.3); margin-bottom:10px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,0.06);">
                                    <img src="{{ Storage::url($study->file_path) }}?v={{ time() }}" style="max-width:100%; max-height:100%; object-fit: contain; opacity:0.9; transition:opacity 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.9" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center; color:var(--text-muted);">
                                        <i class="fas fa-image" style="font-size:36px;"></i>
                                    </div>
                                </div>
                            @else
                                <div style="width:100%; height:120px; border-radius:8px; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; margin-bottom:10px; border:1px solid rgba(255,255,255,0.1);">
                                    <i class="fas fa-file-pdf" style="font-size:40px; color:var(--danger);"></i>
                                </div>
                            @endif

                            @if($study->description)
                                <p style="font-size:12px; color:var(--text-secondary); margin:0 0 12px 0; line-height:1.4; white-space: pre-wrap;">{{ $study->description }}</p>
                            @endif
                        </div>

                        <div class="flex-between" style="margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                            @if(auth()->user()->admin)
                            <a href="{{ Storage::url($study->file_path) }}" download class="btn-modern btn-primary btn-sm" style="flex:1; justify-content:center;">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                            @endif
                            <a href="{{ Storage::url($study->file_path) }}?v={{ time() }}#toolbar=0" target="_blank" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center; margin-left:{{ auth()->user()->admin ? '8px' : '0' }};">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <button wire:click="editStudy({{ $study->id }})" class="btn-modern btn-sm" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2); margin-left:8px;" title="Editar">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button wire:click="deleteStudy({{ $study->id }})" wire:confirm="¿Seguro que deseas eliminar este estudio?" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); margin-left:4px;" title="Eliminar">
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

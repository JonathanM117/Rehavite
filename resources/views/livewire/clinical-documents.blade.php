<div>
    <div class="flex-between mb-16" wire:key="documents-header" style="flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="font-size:14px;color:var(--text-secondary);">
                {{ count($documents) }} documento(s) registrado(s)
            </div>
            <select wire:model.live="typeFilter" class="form-control-modern" style="max-width:220px; padding:6px 12px; font-size:13px;">
                <option value="">Todos los tipos</option>
                @foreach($documentTypes as $docType)
                    <option value="{{ $docType }}">{{ $docType }}</option>
                @endforeach
            </select>
        </div>
        @if(!$showForm)
        <button class="btn-modern btn-primary btn-sm" wire:click="toggleForm">
            <i class="fas fa-plus"></i> Subir Documento
        </button>
        @endif
    </div>

    @if($showForm)
    <div class="glass-card mb-24" wire:key="document-form-wrapper" style="border: 1px dashed var(--accent-primary);">
        <div style="font-weight:600;margin-bottom:16px;font-size:14px;">
            {{ $editingDocId ? 'Editar Documento' : 'Subir Nuevo Documento' }}
        </div>
        <form wire:submit.prevent="saveDoc" x-data="{ uploading: false, progress: 0 }">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Fecha *</label>
                    <input type="date" wire:model="date" class="form-control-modern" max="{{ date('Y-m-d') }}" required>
                    @error('date') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Documento *</label>
                    <select wire:model.live="type" class="form-control-modern" required>
                        @foreach($documentTypes as $docType)
                            <option value="{{ $docType }}">{{ $docType }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($type === 'Incapacidad médica')
                <div class="form-group">
                    <label class="form-label">Vigente hasta</label>
                    <input type="date" wire:model="valid_until" class="form-control-modern">
                    <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:4px;">Al vencer esta fecha, el documento pasará automáticamente a "Histórico".</span>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea wire:model="description" class="form-control-modern" rows="2" placeholder="Ej. Receta médica del Dr. Juan Pérez para tratamiento de dolor lumbar"></textarea>
                @error('description') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false; alert('Error al subir el archivo. Asegúrate de que sea un PDF o una imagen (JPG, PNG) y que no supere los 20MB.')"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <label class="form-label">Documento* {{ $editingDocId ? '(Dejar vacío para conservar el actual)' : '' }}</label>
                <div class="file-upload-wrapper">
                    <input type="file" wire:model="file" class="form-control-modern" id="document-file-input" accept=".jpg,.jpeg,.png,.pdf">
                    <div wire:loading wire:target="file" style="margin-top:8px; font-size:12px; color:var(--accent-primary);">
                        <i class="fas fa-spinner fa-spin"></i> Subiendo archivo...
                    </div>
                </div>
                <span style="font-size:11px;color:var(--text-muted);display:block;margin-top:6px;">PDF, JPG, PNG (máx. 20 MB)</span>
                <!-- Barra de Progreso Alpine JS -->
                <div x-show="uploading" style="margin-top: 10px; width: 100%; background-color: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; height: 6px;">
                    <div style="height: 100%; background-color: var(--accent-primary); transition: width 0.3s;" x-bind:style="'width: ' + progress + '%'"></div>
                </div>
                @error('file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="background:rgba(255,255,255,0.02); border-radius:8px; padding:12px; border:1px solid rgba(255,255,255,0.06);">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; font-weight:600;">
                    <input type="checkbox" wire:model.live="manage_status_manually">
                    Gestionar estado manualmente
                </label>
                @if($manage_status_manually)
                    <select wire:model="manual_status" class="form-control-modern" style="margin-top:10px; max-width:220px;">
                        <option value="vigente">🟢 Vigente</option>
                        <option value="actualizar">🟡 Actualizar</option>
                        <option value="historico">⚪ Histórico</option>
                    </select>
                @endif
            </div>

            <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;">
                <button type="button" class="btn-modern btn-secondary btn-sm" wire:click="cancelEdit" style="flex:1; max-width:200px; text-align:center; justify-content:center;">Cancelar</button>
                <button type="submit" class="btn-modern btn-primary btn-sm" x-bind:disabled="uploading" wire:loading.attr="disabled" wire:target="file, saveDoc" style="flex:1; max-width:200px; text-align:center; justify-content:center;" x-bind:style="uploading ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                    <i class="fas fa-save" wire:loading.remove wire:target="saveDoc"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveDoc"></i>
                    {{ $editingDocId ? 'Actualizar Documento' : 'Guardar Documento' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    @if(session()->has('success'))
        <div style="background:rgba(39, 174, 96, 0.1); color:var(--success); padding:12px; border-radius:8px; margin-bottom:16px; font-size:13px; border:1px solid rgba(39, 174, 96, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-2" wire:key="documents-grid">
        @forelse($documents as $doc)
            @php
                $isPdf = Str::endsWith(strtolower($doc->file_path), '.pdf');
                $meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
                $fechaFormateada = $doc->date->format('d') . ' ' . ucfirst($meses[$doc->date->month - 1]) . ' ' . $doc->date->format('Y');
            @endphp
            <div class="glass-card" style="display:flex; flex-direction:column; gap:12px; padding:16px; position:relative;" wire:key="doc-{{ $doc->id }}">
                <span style="position:absolute; top:12px; right:12px; font-size:11px; background:rgba(255,255,255,0.06); padding:3px 8px; border-radius:10px;" title="{{ $doc->status_label }}">
                    {{ $doc->status_emoji }} {{ $doc->status_label }}
                </span>
                <div class="flex-between" style="padding-right:90px;">
                    <span class="badge-modern badge-info">{{ $doc->type }}</span>
                    <span style="font-size:12px; color:var(--text-muted);">{{ $fechaFormateada }}</span>
                </div>

                @if($doc->description)
                    <div style="font-size:13px; color:var(--text-secondary);">
                        {{ $doc->description }}
                    </div>
                @endif

                <div style="margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                    @if($isPdf)
                        <div style="display:flex; align-items:center; justify-content:center; height:120px; background:rgba(0,0,0,0.2); border-radius:8px; margin-bottom:12px; font-size:40px; color:var(--danger);">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                    @else
                        <div style="height:120px; background:rgba(0,0,0,0.2); border-radius:8px; margin-bottom:12px; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                            <img src="{{ Storage::url($doc->file_path) }}" style="max-width:100%; max-height:100%; object-fit:contain;" alt="Documento">
                        </div>
                    @endif

                    <div class="flex-between">
                        @if(auth()->user()->admin)
                        <a href="{{ Storage::url($doc->file_path) }}" download class="btn-modern btn-primary btn-sm" style="flex:1; justify-content:center;">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                        @endif
                        <a href="{{ Storage::url($doc->file_path) }}{{ $isPdf ? '#toolbar=0' : '' }}" target="_blank" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center; margin-left:{{ auth()->user()->admin ? '8px' : '0' }};">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <button wire:click="editDoc({{ $doc->id }})" class="btn-modern btn-sm" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2); margin-left:8px;" title="Editar">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button wire:click="deleteDoc({{ $doc->id }})" wire:confirm="¿Seguro que deseas eliminar este documento?" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); margin-left:4px;" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 30px; text-align: center; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
                <i class="fas fa-folder-open" style="font-size: 24px; color: var(--text-muted); margin-bottom: 12px;"></i>
                <p style="color: var(--text-secondary); margin: 0;">No hay documentos clínicos registrados.</p>
            </div>
        @endforelse
    </div>

    @if(count($exercises) > 0)
        <div class="flex-between mb-16" style="margin-top:24px; border-top:1px solid rgba(255,255,255,0.06); padding-top:24px;">
            <div style="font-size:14px; font-weight:600; color:var(--text-primary);">
                <i class="fas fa-running" style="color:var(--accent-primary);"></i> Ejercicios subidos en Episodios ({{ count($exercises) }})
            </div>
        </div>
        <div class="grid grid-2" wire:key="exercises-from-consultations-grid">
            @foreach($exercises as $exercise)
                <div class="glass-card" style="display:flex; flex-direction:column; gap:12px; padding:16px;" wire:key="patient-file-exercise-{{ $exercise->id }}">
                    <div class="flex-between">
                        <span class="badge-modern badge-info">Ejercicio</span>
                        <span style="font-size:12px; color:var(--text-muted);">{{ $exercise->date->format('d/m/Y') }}</span>
                    </div>

                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">
                        {{ $exercise->title ?? 'Ejercicio sin título' }}
                    </div>

                    @if($exercise->description)
                        <div style="font-size:12px; color:var(--text-secondary);">{{ Str::limit($exercise->description, 100) }}</div>
                    @endif

                    <div style="margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                        <div class="flex-between">
                            @if(auth()->user()->admin)
                            <a href="{{ Storage::url($exercise->file_path) }}" download class="btn-modern btn-primary btn-sm" style="flex:1; justify-content:center;">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                            @endif
                            <a href="{{ Storage::url($exercise->file_path) }}#toolbar=0" target="_blank" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center; margin-left:{{ auth()->user()->admin ? '8px' : '0' }};">
                                <i class="fas fa-eye"></i> Ver PDF
                            </a>
                        </div>
                        @if($exercise->consultation)
                        <a href="{{ route('admin.consultations.show', $exercise->consultation) }}" style="display:flex; align-items:center; justify-content:center; gap:6px; margin-top:8px; font-size:12px; color:var(--accent-primary); text-decoration:none;">
                            <i class="fas fa-link"></i> Ver en su Episodio ({{ $exercise->consultation->created_at->format('d/m/Y') }})
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

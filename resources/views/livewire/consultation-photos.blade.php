<div>
    <div class="section-header" wire:key="photos-main-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fas fa-camera" style="color:var(--accent-primary);"></i>
            <h3 style="margin:0;">Registro Fotográfico Clínico</h3>
        </div>
        <button class="btn-modern btn-primary btn-sm" type="button" wire:click="toggleUploadForm">
            <i class="fas fa-upload"></i> Subir Foto
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

    {{-- Side-by-Side Progression Viewer --}}
    <div class="glass-card mb-24" style="padding:24px; border:1px solid rgba(255,255,255,0.08);">
        <h4 style="margin:0 0 16px; font-size:15px; font-weight:700; color:var(--text-primary);"><i class="fas fa-columns" style="color:var(--accent-primary); margin-right:6px;"></i> Comparativa Visual de Evolución</h4>
        
        <div style="display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; background:rgba(255,255,255,0.02); padding:12px; border-radius:8px;">
            <div style="flex:1; min-width:200px; display:flex; align-items:center; gap:8px;">
                <span style="font-size:12px; color:var(--text-secondary); white-space:nowrap;">Foto A (Antes):</span>
                <select wire:model.live="comparePhotoId1" class="form-control-modern" style="padding:4px 8px; font-size:12px;">
                    <option value="">Seleccionar foto...</option>
                    @foreach($allPatientPhotos as $p)
                        <option value="{{ $p->id }}">{{ $p->date->format('d/m/Y') }} - {{ $p->title ?: 'Sin título' }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width:200px; display:flex; align-items:center; gap:8px;">
                <span style="font-size:12px; color:var(--text-secondary); white-space:nowrap;">Foto B (Después):</span>
                <select wire:model.live="comparePhotoId2" class="form-control-modern" style="padding:4px 8px; font-size:12px;">
                    <option value="">Seleccionar foto...</option>
                    @foreach($allPatientPhotos as $p)
                        <option value="{{ $p->id }}">{{ $p->date->format('d/m/Y') }} - {{ $p->title ?: 'Sin título' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
            {{-- Photo A --}}
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; border:1px dashed rgba(255,255,255,0.05); text-align:center;">
                @if ($photo1)
                    <h5 style="margin:0 0 8px; font-size:13px; font-weight:600; color:var(--accent-primary);">{{ $photo1->title ?: 'Foto A' }} ({{ $photo1->date->format('d/m/Y') }})</h5>
                    <img src="{{ asset($photo1->file_path) }}" style="max-width:100%; max-height:400px; border-radius:6px; object-fit:contain; box-shadow:0 8px 24px rgba(0,0,0,0.5);">
                    @if ($photo1->description)
                        <p style="font-size:12px; color:var(--text-secondary); margin:8px 0 0; text-align:left;">{{ $photo1->description }}</p>
                    @endif
                @else
                    <div style="height:300px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:13px;">
                        Selecciona la primera imagen para comparar
                    </div>
                @endif
            </div>

            {{-- Photo B --}}
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; border:1px dashed rgba(255,255,255,0.05); text-align:center;">
                @if ($photo2)
                    <h5 style="margin:0 0 8px; font-size:13px; font-weight:600; color:var(--accent-primary);">{{ $photo2->title ?: 'Foto B' }} ({{ $photo2->date->format('d/m/Y') }})</h5>
                    <img src="{{ asset($photo2->file_path) }}" style="max-width:100%; max-height:400px; border-radius:6px; object-fit:contain; box-shadow:0 8px 24px rgba(0,0,0,0.5);">
                    @if ($photo2->description)
                        <p style="font-size:12px; color:var(--text-secondary); margin:8px 0 0; text-align:left;">{{ $photo2->description }}</p>
                    @endif
                @else
                    <div style="height:300px; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:13px;">
                        Selecciona la segunda imagen para comparar
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    @if ($showUploadForm)
        <div class="glass-card mb-24" style="padding:24px; border:1px solid rgba(255,255,255,0.08);">
            <h4 style="margin:0 0 16px; font-size:15px; font-weight:700;">Subir Nueva Foto Clínica</h4>
            
            <form wire:submit.prevent="uploadPhoto">
                <div class="grid grid-2 mb-16">
                    <div class="form-group">
                        <label class="form-label">Selecciona Imagen * (Máx. 10MB)</label>
                        <input type="file" wire:model="photoFile" class="form-control-modern" accept="image/*" required>
                        @error('photoFile') <span style="color:#ef4444; font-size:12px; display:block; margin-top:4px;">{{ $message }}</span> @enderror
                        
                        {{-- Image Preview --}}
                        @if ($photoFile)
                            <div style="margin-top:12px; text-align:center;">
                                <span style="font-size:11px; color:var(--text-muted); display:block; margin-bottom:4px;">Vista previa:</span>
                                <img src="{{ $photoFile->temporaryUrl() }}" style="max-height:150px; border-radius:6px; border:1px solid rgba(255,255,255,0.1);">
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="form-group mb-12">
                            <label class="form-label">Fecha de la Toma *</label>
                            <input type="date" wire:model="date" class="form-control-modern" required>
                            @error('date') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Título / Región Anatomica</label>
                            <input type="text" wire:model="title" class="form-control-modern" placeholder="Ej. Flexión dorsal rodilla derecha">
                            @error('title') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-20">
                    <label class="form-label">Descripción / Hallazgos Clínicos</label>
                    <textarea wire:model="description" class="form-control-modern" rows="2" placeholder="Describa el grado de movilidad, dolor o cicatrización observados..."></textarea>
                    @error('description') <span style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" wire:click="toggleUploadForm" class="btn-modern btn-secondary btn-sm">Cancelar</button>
                    <button type="submit" class="btn-modern btn-primary btn-sm"><i class="fas fa-save"></i> Guardar Imagen</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Gallery Grid of Photos for this Consultation --}}
    <h4 style="margin:24px 0 16px; font-size:15px; font-weight:700; color:var(--text-primary);"><i class="fas fa-images" style="color:var(--accent-primary); margin-right:6px;"></i> Fotos de esta Consulta</h4>
    
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:20px;">
        @forelse($allPatientPhotos->where('consultation_id', $this->consultationId) as $p)
            <div class="glass-card" style="padding:0; overflow:hidden; border:1px solid rgba(255,255,255,0.05); display:flex; flex-direction:column; position:relative; group">
                <img src="{{ asset($p->file_path) }}" style="width:100%; height:160px; object-fit:cover; border-bottom:1px solid rgba(255,255,255,0.05);">
                
                <div style="padding:12px; flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <h5 style="margin:0 0 4px; font-size:13px; font-weight:700; color:var(--text-primary);">{{ $p->title ?: 'Sin título' }}</h5>
                        <p style="font-size:11px; color:var(--text-muted); margin:0 0 6px;"><i class="fas fa-calendar-alt"></i> {{ $p->date->format('d/m/Y') }}</p>
                        @if ($p->description)
                            <p style="font-size:11.5px; color:var(--text-secondary); margin:0; line-height:1.4;">{{ Str::limit($p->description, 80) }}</p>
                        @endif
                    </div>
                    
                    <div style="display:flex; gap:8px; margin-top:12px; border-top:1px solid rgba(255,255,255,0.05); padding-top:10px;">
                        <button type="button" wire:click="$set('comparePhotoId1', '{{ $p->id }}')" class="btn-modern btn-secondary btn-xs" style="flex:1; font-size:10px; padding:3px 6px; justify-content:center;">
                            Comparar A
                        </button>
                        <button type="button" wire:click="$set('comparePhotoId2', '{{ $p->id }}')" class="btn-modern btn-secondary btn-xs" style="flex:1; font-size:10px; padding:3px 6px; justify-content:center;">
                            Comparar B
                        </button>
                        <button type="button" onclick="if(confirm('¿Seguro que deseas eliminar esta imagen?')) { @this.deletePhoto({{ $p->id }}) }" class="btn-modern btn-danger btn-xs" style="padding:3px 6px;" title="Eliminar Imagen">
                            <i class="fas fa-trash" style="font-size:10px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; background:rgba(255,255,255,0.01); border:1px dashed rgba(255,255,255,0.08); padding:30px; border-radius:8px; text-align:center; color:var(--text-muted); font-size:13px;">
                <i class="fas fa-image" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                No se han subido fotos clínicas en esta consulta.
            </div>
        @endforelse
    </div>
</div>

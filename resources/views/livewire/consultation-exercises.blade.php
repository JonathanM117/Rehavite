<div>
    <div class="section-header" wire:key="exercises-main-header">
        <i class="fas fa-running"></i>
        <h3>Ejercicios Asignados</h3>
        <div style="margin-left:auto;">
            <button class="btn-modern btn-primary btn-sm" wire:click="toggleForm">
                <i class="fas fa-{{ $showForm ? 'times' : 'plus' }}"></i>
                {{ $showForm ? 'Cancelar' : 'Subir Ejercicio' }}
            </button>
        </div>
    </div>

    @if($showForm)
    <div class="glass-card mb-24" wire:key="exercise-form-wrapper" style="border: 1px dashed var(--accent-primary);">
        <form wire:submit.prevent="saveExercise">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Fecha *</label>
                    <input type="date" wire:model="date" class="form-control-modern" max="{{ date('Y-m-d') }}" required>
                    @error('date') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Título del Ejercicio</label>
                    <input type="text" wire:model="title" class="form-control-modern" placeholder="Ej: Ejercicios de estiramiento lumbar">
                    @error('title') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción / Indicaciones</label>
                <textarea wire:model="description" class="form-control-modern" rows="2" placeholder="Indicaciones para el paciente..."></textarea>
                @error('description') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group"
                 x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false; alert('Error al subir el archivo. Asegúrate de que sea un PDF y que no supere los 10MB.')"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                <label class="form-label">Archivo PDF *</label>
                <div class="file-upload-wrapper">
                    <input type="file" wire:model="file" class="form-control-modern" id="exercise-file-input" accept=".pdf">
                    <div wire:loading wire:target="file" style="margin-top:8px; font-size:12px; color:var(--accent-primary);">
                        <i class="fas fa-spinner fa-spin"></i> Subiendo archivo...
                    </div>
                </div>
                <small style="color:var(--text-muted); font-size:11px; margin-top:4px; display:block;">Solo se permiten archivos PDF (máx. 10MB)</small>
                @error('file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
                <button type="submit" class="btn-modern btn-primary" wire:loading.attr="disabled" wire:target="file, saveExercise">
                    <i class="fas fa-save" wire:loading.remove wire:target="saveExercise"></i>
                    <i class="fas fa-spinner fa-spin" wire:loading wire:target="saveExercise"></i>
                    Guardar Ejercicio
                </button>
            </div>
        </form>
    </div>
    @endif

    <div wire:key="exercises-list-wrapper">
        @if(session()->has('success'))
            <div class="alert-modern alert-success mb-16">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(count($exercises) > 0)
            <div class="grid grid-2">
                @foreach($exercises as $exercise)
                    <div class="glass-card" wire:key="exercise-item-{{ $exercise->id }}" style="padding:12px; height: 100%; display: flex; flex-direction: column;">
                        <div class="flex-between mb-8">
                            <span style="font-size:13px; font-weight:600; color:var(--text-primary);">
                                <i class="fas fa-file-pdf" style="color:var(--danger); margin-right:4px;"></i>
                                {{ $exercise->title ?? 'Ejercicio sin título' }}
                            </span>
                            <span style="font-size:11px;color:var(--text-muted);">{{ $exercise->date->format('d/m/Y') }}</span>
                        </div>

                        <div style="flex-grow:1;">
                            <div style="width:100%; height:80px; border-radius:8px; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; margin-bottom:10px; border:1px solid rgba(255,255,255,0.1);">
                                <i class="fas fa-file-pdf" style="font-size:36px; color:var(--danger);"></i>
                            </div>

                            @if($exercise->description)
                                <p style="font-size:12px; color:var(--text-secondary); margin:0 0 12px 0; line-height:1.4;">{{ Str::limit($exercise->description, 80) }}</p>
                            @endif
                        </div>

                        <div class="flex-between" style="margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                            @if(auth()->user()->admin)
                            <a href="{{ Storage::url($exercise->file_path) }}" download class="btn-modern btn-primary btn-sm" style="flex:1; justify-content:center;">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                            @endif
                            <a href="{{ Storage::url($exercise->file_path) }}#toolbar=0" target="_blank" class="btn-modern btn-secondary btn-sm" style="flex:1; justify-content:center; margin-left:{{ auth()->user()->admin ? '8px' : '0' }};">
                                <i class="fas fa-eye"></i> Ver PDF
                            </a>
                            <button wire:click="deleteExercise({{ $exercise->id }})" wire:confirm="¿Seguro que deseas eliminar este ejercicio?" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2); margin-left:8px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-running"></i>
                <p>No hay ejercicios asignados para esta consulta</p>
            </div>
        @endif
    </div>

    <script>
        window.addEventListener('alert', event => {
            alert(event.detail.message || event.detail[0].message);
        });
    </script>
</div>

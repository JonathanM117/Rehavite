<div>
    @if(session('success'))
        <div class="alert-modern alert-success mb-24">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">

            {{-- LEFT COLUMN: Landing Texts --}}
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div class="glass-card" style="padding:24px;">
                    <h3 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--accent-primary);">
                        <i class="fas fa-image"></i> Sección Principal (Hero)
                    </h3>

                    <div class="form-group">
                        <label class="form-label">Título Principal</label>
                        <input type="text" wire:model="hero_title" class="form-control-modern" placeholder="En Rehavité buscamos...">
                        @error('hero_title') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtítulo</label>
                        <input type="text" wire:model="hero_subtitle" class="form-control-modern" placeholder="Te estamos esperando">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Texto del Botón</label>
                        <input type="text" wire:model="hero_button" class="form-control-modern" placeholder="AGENDA TU CITA HOY">
                    </div>

                    {{-- Hero Image Upload --}}
                    <div class="form-group"
                         x-data="{ uploading: false }"
                         x-on:livewire-upload-start="uploading = true"
                         x-on:livewire-upload-finish="uploading = false"
                         x-on:livewire-upload-error="uploading = false; alert('Error al subir la imagen.')">
                        <label class="form-label">Imagen de Fondo (Hero)</label>
                        @if($currentHeroImage)
                            <div style="margin-bottom:12px; border-radius:8px; overflow:hidden; height:120px;">
                                <img src="{{ Storage::url($currentHeroImage) }}" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <p style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Imagen actual. Sube una nueva para reemplazarla.</p>
                        @endif
                        <input type="file" wire:model="hero_image_upload" class="form-control-modern" accept="image/jpg,image/jpeg,image/png,image/webp">
                        <div wire:loading wire:target="hero_image_upload" style="margin-top:8px; font-size:12px; color:var(--accent-primary);">
                            <i class="fas fa-spinner fa-spin"></i> Procesando imagen...
                        </div>
                        @if($this->hero_image_upload)
                            <div style="margin-top:8px; border-radius:8px; overflow:hidden; height:80px;">
                                <img src="{{ $this->hero_image_upload->temporaryUrl() }}" style="width:100%; height:100%; object-fit:cover; opacity:0.7;">
                                <p style="font-size:11px; color:var(--accent-primary); margin-top:4px;">Vista previa de nueva imagen</p>
                            </div>
                        @endif
                        @error('hero_image_upload') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="glass-card" style="padding:24px;">
                    <h3 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--accent-primary);">
                        <i class="fas fa-quote-left"></i> Frase Motivacional
                    </h3>
                    <div class="form-group">
                        <textarea wire:model="quote_text" class="form-control-modern" rows="4" placeholder='"Nos mueve la idea de ayudar..."'></textarea>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Contact & Schedule --}}
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div class="glass-card" style="padding:24px;">
                    <h3 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--accent-primary);">
                        <i class="fas fa-address-card"></i> Información de Contacto
                    </h3>
                    <div class="form-group">
                        <label class="form-label">Teléfono / WhatsApp</label>
                        <input type="text" wire:model="contact_phone" class="form-control-modern" placeholder="6181102286">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <textarea wire:model="contact_address" class="form-control-modern" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL del mapa (Google Maps)</label>
                        <input type="url" wire:model="maps_url" class="form-control-modern" placeholder="https://maps.app.goo.gl/...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL de WhatsApp</label>
                        <input type="url" wire:model="whatsapp_url" class="form-control-modern" placeholder="https://wa.me/526181...">
                    </div>
                </div>

                <div class="glass-card" style="padding:24px;">
                    <h3 style="margin:0 0 20px; font-size:16px; font-weight:700; color:var(--accent-primary);">
                        <i class="fas fa-clock"></i> Horario de Atención
                    </h3>
                    <div class="form-group">
                        <label class="form-label">Entre semana</label>
                        <input type="text" wire:model="schedule_weekdays" class="form-control-modern" placeholder="Lunes - Viernes: 10:00am – 08:00pm">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sábado</label>
                        <input type="text" wire:model="schedule_saturday" class="form-control-modern" placeholder="Sábado: 10:00am – 02:00pm">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Domingo</label>
                        <input type="text" wire:model="schedule_sunday" class="form-control-modern" placeholder="Domingo: Cerrado">
                    </div>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        <div style="text-align:right; margin-top:24px;">
            <button type="submit" class="btn-modern btn-primary" wire:loading.attr="disabled" wire:target="save">
                <i class="fas fa-save" wire:loading.remove wire:target="save"></i>
                <i class="fas fa-spinner fa-spin" wire:loading wire:target="save"></i>
                Guardar Configuración
            </button>
        </div>
    </form>

    <script>
        window.addEventListener('alert', event => {
            alert(event.detail.message);
        });
    </script>
</div>

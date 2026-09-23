<div class="container" style="max-width: 800px; margin: 40px auto; padding: 0 16px;">
    @if ($bookingSuccess)
        <div class="glass-card" style="text-align: center; padding: 40px 24px; border: 1px solid rgba(20, 184, 166, 0.3);">
            <div style="font-size: 64px; color: #10b981; margin-bottom: 24px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px;">¡Cita Reservada con Éxito!</h2>
            <p style="color: var(--text-secondary); margin-bottom: 32px; font-size: 16px; line-height: 1.6;">
                Tu solicitud de cita para el día <strong>{{ $createdAppointment->start_time->format('d/m/Y') }}</strong> a las <strong>{{ $createdAppointment->start_time->format('H:i') }}</strong> con <strong>{{ $createdAppointment->user->name }}</strong> ha sido registrada.<br>
                Nos comunicaremos contigo a la brevedad para confirmar tu espacio.
            </p>
            <button wire:click="$set('bookingSuccess', false)" class="btn-modern btn-primary" style="padding: 12px 30px; font-size: 14px;">
                Reservar otra cita
            </button>
        </div>
    @else
        <div class="glass-card" style="padding: 32px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="text-align: center; margin-bottom: 32px;">
                <h1 style="font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #14b8a6 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 8px;">Agenda tu Cita en Línea</h1>
                <p style="color: var(--text-secondary); font-size: 14px;">Selecciona tu especialista, el día de tu preferencia y tu horario.</p>
            </div>

            @if (session()->has('error'))
                <div class="alert-modern alert-danger mb-24" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); padding: 12px; border-radius: 8px;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="bookAppointment">
                <div class="grid grid-2 mb-24">
                    <div class="form-group">
                        <label class="form-label">1. Selecciona Especialista *</label>
                        <select wire:model.live="selectedTherapistId" class="form-control-modern" required>
                            <option value="">Elegir fisioterapeuta...</option>
                            @foreach ($therapists as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedTherapistId') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">2. Fecha de tu Cita *</label>
                        <input type="date" wire:model.live="selectedDate" class="form-control-modern" min="{{ date('Y-m-d') }}" required>
                        @error('selectedDate') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if ($selectedTherapistId)
                    <div class="mb-24">
                        <label class="form-label">3. Horarios Disponibles *</label>
                        @if (count($availableSlots) > 0)
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; margin-top: 10px;">
                                @foreach ($availableSlots as $slot)
                                    <button type="button" 
                                            wire:click="$set('selectedTimeSlot', '{{ $slot }}')" 
                                            class="btn-modern btn-sm" 
                                            style="justify-content: center; font-size: 13px; font-weight:600; {{ $selectedTimeSlot === $slot ? 'background: var(--accent-gradient); color: white; border: none;' : 'background: rgba(255,255,255,0.05); color: var(--text-primary); border: 1px solid rgba(255,255,255,0.1);' }}">
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); padding: 20px; border-radius: 8px; text-align: center; color: var(--text-muted); font-size: 13px;">
                                <i class="fas fa-calendar-times" style="font-size: 24px; margin-bottom: 8px; display: block; color: var(--text-muted);"></i>
                                No hay horarios disponibles para la fecha seleccionada. Intente con otro día.
                            </div>
                        @endif
                        @error('selectedTimeSlot') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 24px;">
                    <label class="form-label" style="font-weight: 700; font-size: 15px; margin-bottom: 16px; display: block;">4. Tus Datos de Contacto</label>
                    
                    <div class="form-group mb-16">
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" wire:model="name" class="form-control-modern" placeholder="Ej. Juan Pérez López" required>
                        @error('name') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-2 mb-16">
                        <div class="form-group">
                            <label class="form-label">Correo Electrónico *</label>
                            <input type="email" wire:model="email" class="form-control-modern" placeholder="ejemplo@correo.com" required>
                            @error('email') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de Teléfono *</label>
                            <input type="tel" wire:model="phone" class="form-control-modern" placeholder="Mínimo 10 dígitos" required>
                            @error('phone') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group mb-24">
                        <label class="form-label">Notas o Síntomas (Opcional)</label>
                        <textarea wire:model="notes" class="form-control-modern" rows="3" placeholder="Coméntanos brevemente el motivo de tu consulta..."></textarea>
                        @error('notes') <span class="error-text" style="color: #ef4444; font-size: 12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="text-align: center; margin-top: 32px;">
                    <button type="submit" class="btn-modern btn-primary" style="padding: 12px 40px; font-size: 15px; font-weight: 700; width: 100%; max-width: 300px; justify-content: center;">
                        Agendar Cita <i class="fas fa-calendar-check" style="margin-left: 8px;"></i>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<div x-data="{ showCreateModal: @entangle('showCreateModal') }">
    <div class="flex-between mb-24">
        <div>
            <h1 style="font-size:24px; font-weight:700; margin:0;"><i class="fas fa-calendar-alt" style="margin-right:8px; color:var(--accent-primary);"></i> Agenda y Reservas</h1>
            <p style="color:var(--text-secondary); margin:4px 0 0; font-size:13px;">Gestiona las citas de pacientes y configura los horarios de atención.</p>
        </div>
        <button type="button" wire:click="openCreateModal" class="btn-modern btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nueva Cita
        </button>
    </div>

    {{-- Tabs --}}
    <div class="tabs-modern mb-24" style="border-bottom:1px solid var(--border-color); display:flex; gap:16px;">
        <button type="button" 
                wire:click="$set('activeTab', 'appointments')" 
                class="tab-item {{ $activeTab === 'appointments' ? 'active' : '' }}"
                style="padding:12px 16px; font-size:14px; font-weight:600; border:none; background:none; color:{{ $activeTab === 'appointments' ? 'var(--accent-primary)' : 'var(--text-secondary)' }}; border-bottom:{{ $activeTab === 'appointments' ? '2px solid var(--accent-primary)' : 'none' }}; cursor:pointer;">
            <i class="fas fa-list-ul"></i> Listado de Citas
        </button>
        <button type="button" 
                wire:click="$set('activeTab', 'schedules')" 
                class="tab-item {{ $activeTab === 'schedules' ? 'active' : '' }}"
                style="padding:12px 16px; font-size:14px; font-weight:600; border:none; background:none; color:{{ $activeTab === 'schedules' ? 'var(--accent-primary)' : 'var(--text-secondary)' }}; border-bottom:{{ $activeTab === 'schedules' ? '2px solid var(--accent-primary)' : 'none' }}; cursor:pointer;">
            <i class="fas fa-clock"></i> Horarios de Atención
        </button>
    </div>

    @if(session()->has('success'))
        <div class="alert-modern alert-success mb-24" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2); padding:12px; border-radius:8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ═══ TAB: APPOINTMENTS ═══ --}}
    @if ($activeTab === 'appointments')
        <div class="glass-card mb-24" style="padding: 20px;">
            {{-- Filters --}}
            <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:20px;">
                @if(auth()->user()->admin)
                    <div class="form-group" style="margin-bottom:0; max-width:200px; flex:1;">
                        <select wire:model.live="filterTherapist" class="form-control-modern" style="padding:6px 12px; font-size:13px;">
                            <option value="">Todos los fisios</option>
                            @foreach($therapists as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group" style="margin-bottom:0; max-width:180px; flex:1;">
                    <select wire:model.live="filterStatus" class="form-control-modern" style="padding:6px 12px; font-size:13px;">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0; max-width:180px; flex:1;">
                    <input type="date" wire:model.live="filterDate" class="form-control-modern" style="padding:6px 12px; font-size:13px;">
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table-modern" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Fecha y Hora</th>
                            <th style="text-align:left;">Paciente</th>
                            <th style="text-align:left;">Contacto</th>
                            @if(auth()->user()->admin)
                                <th style="text-align:left;">Fisioterapeuta</th>
                            @endif
                            <th style="text-align:left;">Estado</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td style="padding:12px 8px;">
                                    <div style="font-weight:600; color:var(--text-primary);">{{ $appointment->start_time->format('d/m/Y') }}</div>
                                    <div style="font-size:12px; color:var(--text-secondary);">{{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</div>
                                </td>
                                <td style="padding:12px 8px;">
                                    @if ($appointment->patient_id)
                                        <a href="{{ route('admin.patients.show', $appointment->patient_id) }}" style="color:var(--accent-primary); font-weight:600; text-decoration:none;">
                                            {{ $appointment->name }} <i class="fas fa-external-link-alt" style="font-size:10px;"></i>
                                        </a>
                                    @else
                                        <span style="color:var(--text-primary); font-weight:600;">{{ $appointment->name }}</span>
                                        <span style="font-size:10px; background:rgba(245,158,11,0.15); color:#f59e0b; padding:2px 6px; border-radius:4px; margin-left:6px;">Invitado</span>
                                    @endif
                                    @if($appointment->notes)
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $appointment->notes }}</div>
                                    @endif
                                </td>
                                <td style="padding:12px 8px;">
                                    <div style="font-size:13px; color:var(--text-primary);"><i class="fas fa-envelope" style="width:16px; color:var(--text-muted);"></i> {{ $appointment->email }}</div>
                                    <div style="font-size:13px; color:var(--text-primary); margin-top:2px;"><i class="fas fa-phone-alt" style="width:16px; color:var(--text-muted);"></i> {{ $appointment->phone }}</div>
                                </td>
                                @if(auth()->user()->admin)
                                    <td style="padding:12px 8px; color:var(--text-primary);">{{ $appointment->user->name ?? '--' }}</td>
                                @endif
                                <td style="padding:12px 8px;">
                                    @php
                                        $badgeBg = [
                                            'pending' => 'rgba(245,158,11,0.15)',
                                            'confirmed' => 'rgba(16,185,129,0.15)',
                                            'cancelled' => 'rgba(239,68,68,0.15)',
                                        ][$appointment->status] ?? 'rgba(255,255,255,0.1)';
                                        $badgeColor = [
                                            'pending' => '#f59e0b',
                                            'confirmed' => '#10b981',
                                            'cancelled' => '#ef4444',
                                        ][$appointment->status] ?? 'var(--text-primary)';
                                    @endphp
                                    <span style="font-size:12px; font-weight:600; padding:4px 8px; border-radius:6px; background:{{ $badgeBg }}; color:{{ $badgeColor }}; display:inline-block;">
                                        {{ $appointment->status_label }}
                                    </span>
                                </td>
                                <td style="padding:12px 8px; text-align:right;">
                                    <div style="display:inline-flex; gap:8px;">
                                        @if($appointment->status === 'pending')
                                            <button wire:click="confirmAppointment({{ $appointment->id }})" class="btn-modern btn-success btn-sm" style="padding:4px 8px; font-size:11px;" title="Confirmar Cita">
                                                <i class="fas fa-check"></i> Confirmar
                                            </button>
                                        @endif

                                        @if($appointment->status !== 'cancelled')
                                            <button wire:click="cancelAppointment({{ $appointment->id }})" class="btn-modern btn-danger btn-sm" style="padding:4px 8px; font-size:11px;" title="Cancelar Cita">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                            
                                            <a href="{{ $this->getWhatsAppLink($appointment->id) }}" target="_blank" class="btn-modern btn-secondary btn-sm" style="padding:4px 8px; font-size:11px; display:inline-flex; align-items:center; gap:4px;" title="Enviar recordatorio por WhatsApp">
                                                <i class="fab fa-whatsapp" style="color:#25d366; font-size:13px;"></i> WhatsApp
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:40px; text-align:center; color:var(--text-muted);">
                                    <i class="fas fa-calendar-times" style="font-size:32px; display:block; margin-bottom:12px; color:var(--text-muted);"></i>
                                    No se encontraron citas agendadas para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px;">
                {{ $appointments->links() }}
            </div>
        </div>
    @endif

    {{-- ═══ TAB: SCHEDULES ═══ --}}
    @if ($activeTab === 'schedules')
        <div class="glass-card mb-24" style="max-width:800px; padding:24px;">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:16px;">Configura tu Horario Laboral</h3>
            
            @if(auth()->user()->admin)
                <div class="form-group mb-24" style="max-width:300px;">
                    <label class="form-label">Selecciona Fisioterapeuta para editar horario</label>
                    <select wire:model.live="targetTherapistId" class="form-control-modern">
                        @foreach($therapists as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <form wire:submit.prevent="saveSchedules">
                <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
                    @foreach($schedules as $dayNum => $data)
                        <div style="display:grid; grid-template-columns: 140px 100px 1fr 1fr; gap:16px; align-items:center; padding:12px; background:rgba(255,255,255,0.02); border-radius:8px; border:1px solid rgba(255,255,255,0.04);">
                            <span style="font-weight:600; color:var(--text-primary);">{{ $data['day_name'] }}</span>
                            
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" wire:model="schedules.{{ $dayNum }}.is_active" style="cursor:pointer;">
                                <span style="font-size:13px; color:var(--text-primary);">Activo</span>
                            </label>

                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; color:var(--text-muted); display:block; margin-bottom:2px;">Inicio</label>
                                <input type="time" wire:model="schedules.{{ $dayNum }}.start_time" class="form-control-modern" style="padding:4px 8px; font-size:13px;" @if(!$data['is_active']) disabled @endif>
                            </div>

                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-size:11px; color:var(--text-muted); display:block; margin-bottom:2px;">Fin</label>
                                <input type="time" wire:model="schedules.{{ $dayNum }}.end_time" class="form-control-modern" style="padding:4px 8px; font-size:13px;" @if(!$data['is_active']) disabled @endif>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- CREATE APPOINTMENT MODAL --}}
    <div x-show="showCreateModal" x-cloak style="position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center; padding:16px;">
        <div class="glass-card" style="max-width:550px; width:100%; padding:28px; box-shadow:0 20px 50px rgba(0,0,0,0.5);">
            <div class="flex-between mb-20" style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px;">
                <h3 style="margin:0; font-size:18px; font-weight:700;"><i class="fas fa-calendar-plus" style="margin-right:6px; color:var(--accent-primary);"></i> Agendar Nueva Cita</h3>
                <button type="button" @click="showCreateModal = false" style="background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size:18px;"><i class="fas fa-times"></i></button>
            </div>

            <form wire:submit.prevent="createAppointment">
                @if(auth()->user()->admin)
                    <div class="form-group mb-16">
                        <label class="form-label">Asignar a Fisioterapeuta *</label>
                        <select wire:model="createTherapistId" class="form-control-modern" required>
                            @foreach($therapists as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('createTherapistId') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="form-group mb-16">
                    <label class="form-label">Nombre del Paciente *</label>
                    <input type="text" wire:model="createName" class="form-control-modern" placeholder="Juan Pérez" required>
                    @error('createName') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-2 mb-16">
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" wire:model="createEmail" class="form-control-modern" placeholder="paciente@correo.com" required>
                        @error('createEmail') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Número de Teléfono *</label>
                        <input type="text" wire:model="createPhone" class="form-control-modern" placeholder="10 dígitos" required>
                        @error('createPhone') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-2 mb-16">
                    <div class="form-group">
                        <label class="form-label">Fecha *</label>
                        <input type="date" wire:model="createDate" class="form-control-modern" required>
                        @error('createDate') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hora *</label>
                        <input type="time" wire:model="createTime" class="form-control-modern" required>
                        @error('createTime') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group mb-24">
                    <label class="form-label">Notas Adicionales</label>
                    <textarea wire:model="createNotes" class="form-control-modern" rows="2" placeholder="Observaciones de la reserva..."></textarea>
                    @error('createNotes') <span class="error-text" style="color:#ef4444; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" @click="showCreateModal = false" class="btn-modern btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-modern btn-primary"><i class="fas fa-save"></i> Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

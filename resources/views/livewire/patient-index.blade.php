<div>
    <div class="flex-between mb-24">
        <div class="flex-center" style="gap:12px; flex-wrap:wrap;">
            <div class="search-bar" style="max-width: 400px; width:100%;">
                <i class="fas fa-search"></i>
                <input type="text" wire:model.live="search" placeholder="Busca por nombre, diagnóstico o teléfono..." style="width:100%;" autofocus>
            </div>
            <button wire:click="$toggle('birthdayFilter')" 
                    class="btn-modern btn-sm {{ $birthdayFilter ? 'btn-primary' : 'btn-secondary' }}"
                    title="Filtrar cumpleaños del mes">
                <i class="fas fa-birthday-cake"></i> 
                {{ $birthdayFilter ? 'Cumpleaños del mes ✕' : 'Cumpleaños' }}
            </button>
            @if(auth()->user()->admin)
            <select wire:model.live="therapistFilter" class="form-control-modern" style="max-width:220px; padding:6px 12px; font-size:13px;">
                <option value="">Todos los fisios</option>
                @foreach($therapists as $therapist)
                    <option value="{{ $therapist->id }}">{{ $therapist->name }}</option>
                @endforeach
            </select>
            @endif
        </div>
        <a href="{{ route('admin.patients.create') }}" class="btn-modern btn-primary">
            <i class="fas fa-plus"></i> Nuevo Paciente
        </a>
    </div>

    <div class="glass-card" style="padding:0; overflow:hidden; position:relative;">
        {{-- Loading Overlay --}}
        <div wire:loading style="position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-primary); animation: loading 1.5s infinite linear; z-index:100;"></div>

        <div style="overflow-x: auto; width: 100%;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th wire:click="sort('id')" style="cursor:pointer; user-select:none;">
                        # @if($sortBy === 'id') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th wire:click="sort('name')" style="cursor:pointer; user-select:none;">
                        Paciente @if($sortBy === 'name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th>Diagnóstico</th>
                    <th wire:click="sort('birth_date')" style="cursor:pointer; user-select:none;">
                        Edad @if($sortBy === 'birth_date') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th>Teléfono</th>
                    @if(auth()->user()->admin)
                    <th>Fisioterapeuta</th>
                    @endif
                    <th>Última Actividad</th>
                    <th wire:click="sort('created_at')" style="cursor:pointer; user-select:none;">
                        Creado @if($sortBy === 'created_at') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    @php
                        $isOwner = $patient->user_id === auth()->id();
                        $isAdmin = auth()->user()->admin;
                        $isCollaborator = !$isOwner && !$isAdmin;
                    @endphp
                    <tr wire:key="patient-row-{{ $patient->id }}">
                        <td>
                            <span style="color:var(--text-muted);font-size:12px;">{{ $patient->expediente_id }}</span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-gradient);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:600;color:white;margin-right:12px;">
                                    {{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->name), 0, 1)) }}{{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->last_name), 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text-primary);">
                                        {{ $patient->name }} {{ $patient->last_name }}
                                        @if($patient->status !== 'active')
                                            <span class="badge-modern badge-{{ $patient->status }}" style="font-size:9px; padding:2px 6px; margin-left:4px;">
                                                {{ $patient->status === 'inactive' ? 'Inactivo' : 'Alta' }}
                                            </span>
                                        @endif
                                        @if($isCollaborator)
                                            <span style="font-size:9px; padding:2px 6px; margin-left:4px; background:rgba(99,102,241,0.15); color:#818cf8; border-radius:4px;">Colaborador</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">{!! ($isOwner || $isAdmin) ? e($patient->email) : '<i class="fas fa-eye-slash" title="Oculto" style="opacity:0.5;"></i>' !!}</div>
                                </div>
                            </div>
                        </td>
                        <td style="max-width:200px;">
                            <span style="font-size:13px; color:var(--text-secondary);">{{ Str::limit($patient->diagnosis, 50) }}</span>
                        </td>
                        <td>
                            @if($patient->birth_date)
                                <span class="badge-modern badge-active" style="background:rgba(255,255,255,0.05);">{{ $patient->age }} años</span>
                                @if($patient->birth_date->format('m-d') === now()->format('m-d'))
                                    <span title="¡Hoy es su cumpleaños!" style="margin-left:4px; cursor:help;">🎂</span>
                                @elseif($patient->birth_date->format('m') === now()->format('m'))
                                    <span title="Cumpleaños este mes ({{ $patient->birth_date->format('d') }})" style="margin-left:4px; font-size:11px; color:var(--text-muted); cursor:help;">🎂 {{ $patient->birth_date->format('d') }}</span>
                                @endif
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>{!! ($isOwner || $isAdmin) ? e($patient->phone ?? '—') : '<i class="fas fa-eye-slash" title="Oculto" style="color:var(--text-muted); opacity:0.5;"></i>' !!}</td>
                        @if(auth()->user()->admin)
                        <td>
                            <span style="font-size:12px; color:var(--text-secondary);">
                                <i class="fas fa-user-md" style="font-size:10px; color:var(--accent-primary); margin-right:4px;"></i>
                                {{ $patient->user->name ?? '—' }}
                            </span>
                        </td>
                        @endif
                        <td style="font-size:11px; color:var(--text-secondary); max-width:150px;">
                            @php $latestLog = $patient->auditLogs()->first(); @endphp
                            @if($latestLog)
                                {{ Str::limit($latestLog->description, 35) }}<br>
                                <small style="color:var(--text-muted);">{{ $latestLog->created_at->diffForHumans() }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--text-secondary);">
                            {{ $patient->created_at->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;">
                            <div class="flex-center" style="justify-content:flex-end;gap:6px;">
                                @if($patient->birth_date && $patient->birth_date->format('m-d') === now()->format('m-d') && $patient->phone)
                                    <a href="https://wa.me/52{{ preg_replace('/[^0-9]/', '', $patient->phone) }}?text={{ urlencode('¡Feliz cumpleaños ' . $patient->name . '! 🎂🎉 De parte de todo el equipo de Rehavité, te deseamos un excelente día. ¡Un abrazo!') }}" 
                                       target="_blank" class="btn-icon" title="Enviar felicitación por WhatsApp" style="color:#25D366;">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.patients.show', $patient) }}" class="btn-icon" title="Ver expediente">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                @if($isOwner || $isAdmin)
                                    <a href="{{ route('admin.patients.edit', $patient) }}" class="btn-icon" title="Editar">
                                        <i class="fas fa-pen text-secondary"></i>
                                    </a>
                                    <button type="button" class="btn-icon" title="Eliminar" style="color:var(--danger); border:none; background:none;" onclick="openDeleteModal('{{ route('admin.patients.destroy', $patient) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <span class="btn-icon" style="opacity:0.2; cursor:not-allowed;" title="Solo lectura"><i class="fas fa-pen"></i></span>
                                    <span class="btn-icon" style="opacity:0.2; cursor:not-allowed;" title="Solo lectura"><i class="fas fa-trash"></i></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->admin ? '8' : '7' }}">
                            <div class="empty-state" style="padding:60px 20px;">
                                <div style="font-size:48px; color:rgba(255,255,255,0.1); margin-bottom:16px;">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <p style="color:var(--text-secondary); font-size:18px;">No encontramos pacientes que coincidan</p>
                                <p style="font-size:14px; color:var(--text-muted);">Intenta con otros términos o registra un nuevo paciente.</p>
                                @if($search)
                                    <button wire:click="$set('search', '')" class="btn-modern btn-secondary btn-sm" style="margin-top:16px;">
                                        Limpiar búsqueda
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($patients->hasPages())
        <div class="pagination-modern" style="margin-top:24px;">
            {{ $patients->links() }}
        </div>
    @endif

    <style>
        @keyframes loading {
            0% { left: -30%; width: 30%; }
            50% { left: 40%; width: 60%; }
            100% { left: 100%; width: 30%; }
        }
    </style>

    {{-- MODAL ELIMINAR PACIENTE --}}
    <div id="deleteModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="glass-card" style="max-width: 420px; width: 90%; text-align: center; padding: 40px 30px; animation: slideDown 0.3s ease;">
            <div style="font-size: 54px; color: var(--danger); margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin-bottom: 12px; font-size: 22px;">&iquest;Eliminar Paciente?</h3>
            <p style="color: var(--text-secondary); margin-bottom: 30px; font-size: 15px; line-height: 1.5;">
                Esta acci&oacute;n es <strong>permanente</strong> y eliminar&aacute; todo su historial m&eacute;dico, notas de evoluci&oacute;n, estudios y pagos asociados. &iquest;Est&aacute;s completamente seguro?
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="btn-modern btn-secondary" onclick="closeDeleteModal()" style="flex: 1;">Cancelar</button>
                <form id="deleteForm" method="POST" action="" style="flex: 1; margin: 0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-modern" style="background: var(--danger); color: white; border: none; width: 100%;">
                        <i class="fas fa-trash" style="margin-right: 6px;"></i> S&iacute;, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        function openDeleteModal(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
</div>

<div x-data="{ showFilters: {{ ($therapistFilter || $statusFilter || $birthDateStart || $birthDateEnd) ? 'true' : 'false' }} }">
    <div class="flex-between mb-24">
        <div class="flex-center" style="gap:12px; flex-wrap:wrap;">
            <div class="search-bar" style="max-width: 400px; width:100%;">
                <i class="fas fa-search"></i>
                <input type="text" wire:model.live="search" placeholder="Busca por nombre, padecimiento o teléfono..." style="width:100%;" autofocus>
            </div>
            <button wire:click="$toggle('birthdayFilter')"
                    class="btn-modern btn-sm {{ $birthdayFilter ? 'btn-primary' : 'btn-secondary' }}"
                    title="Filtrar cumpleaños del mes">
                <i class="fas fa-birthday-cake"></i>
                {{ $birthdayFilter ? 'Cumpleaños del mes ✕' : 'Cumpleaños' }}
            </button>
            <button type="button" @click="showFilters = !showFilters"
                    class="btn-modern btn-sm {{ ($therapistFilter || $statusFilter || $birthDateStart || $birthDateEnd) ? 'btn-primary' : 'btn-secondary' }}">
                <i class="fas fa-filter"></i> Filtros
                <i class="fas fa-caret-down" style="font-size:10px; margin-left:4px;"></i>
            </button>
        </div>
        <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
            <a href="{{ route('admin.patients.create') }}" class="btn-modern btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Paciente
            </a>
        </div>
    </div>

    <div x-show="showFilters" x-cloak x-transition class="glass-card mb-24" style="padding:16px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            @if(auth()->user()->admin)
            <div class="form-group" style="margin:0;">
                <label class="form-label">Fisioterapeuta</label>
                <select wire:model.live="therapistFilter" class="form-control-modern" style="min-width:200px;">
                    <option value="">Todos los fisios</option>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}">{{ $therapist->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group" style="margin:0;">
                <label class="form-label">Estado</label>
                <select wire:model.live="statusFilter" class="form-control-modern" style="min-width:180px;">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="seguimiento">Seguimiento</option>
                    <option value="alta_clinica">Alta clínica</option>
                    <option value="suspendido">Suspendido</option>
                    <option value="abandono">Abandono terapéutico</option>
                    <option value="referido">Referido</option>
                    <option value="inactive">Inactivo</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Fecha de nacimiento desde</label>
                <input type="date" wire:model.live="birthDateStart" class="form-control-modern">
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Fecha de nacimiento hasta</label>
                <input type="date" wire:model.live="birthDateEnd" class="form-control-modern">
            </div>
            @if($therapistFilter || $statusFilter || $birthDateStart || $birthDateEnd)
            <button type="button" class="btn-modern btn-secondary btn-sm" wire:click="resetFilters">
                <i class="fas fa-times"></i> Limpiar filtros
            </button>
            @endif
        </div>
    </div>

    <div class="glass-card patient-table-desktop" style="padding:0; overflow:hidden; position:relative;">
        {{-- Loading Overlay --}}
        <div wire:loading style="position:absolute; top:0; left:0; right:0; height:3px; background:var(--accent-primary); animation: loading 1.5s infinite linear; z-index:100;"></div>

        <div style="overflow-x: auto; width: 100%;">
        <table class="modern-table">
            <thead>
                <tr>
                    <th wire:click="sort('name')" style="cursor:pointer; user-select:none;">
                        Paciente @if($sortBy === 'name') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th>Padecimiento</th>
                    <th wire:click="sort('birth_date')" style="cursor:pointer; user-select:none;">
                        Edad @if($sortBy === 'birth_date') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th>Contacto</th>
                    @if(auth()->user()->admin)
                    <th wire:click="sort('therapist')" style="cursor:pointer; user-select:none;">
                        Fisioterapeuta @if($sortBy === 'therapist') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    @endif
                    <th wire:click="sort('last_activity')" style="cursor:pointer; user-select:none;">
                        Última Actividad @if($sortBy === 'last_activity') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th wire:click="sort('created_at')" style="cursor:pointer; user-select:none;">
                        Creado @if($sortBy === 'created_at') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" style="font-size:10px; margin-left:4px; color:var(--accent-primary);"></i> @else <i class="fas fa-sort" style="font-size:10px; margin-left:4px; opacity:0.3;"></i> @endif
                    </th>
                    <th style="text-align:center; min-width:110px;">Acciones</th>
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
                            <a href="{{ route('admin.patients.show', $patient) }}" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit;">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--accent-gradient);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:600;color:white;margin-right:12px;flex-shrink:0;">
                                    {{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->name), 0, 1)) }}{{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->last_name), 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text-primary);">
                                        {{ $patient->name }} {{ $patient->last_name }}
                                        @if(!in_array($patient->status, ['activo','active']))
                                            <span class="badge-modern badge-{{ $patient->status }}" style="font-size:9px; padding:2px 6px; margin-left:4px;">
                                                {{ $patient->status_label }}
                                            </span>
                                        @endif
                                        @if($isCollaborator)
                                            <span style="font-size:9px; padding:2px 6px; margin-left:4px; background:rgba(99,102,241,0.15); color:#818cf8; border-radius:4px;">Colaborador</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $patient->expediente_id }}</div>
                                </div>
                            </a>
                        </td>
                        <td style="max-width:200px;">
                            <span style="font-size:13px; color:var(--text-secondary);">{{ Str::limit($patient->diagnosis, 50) }}</span>
                        </td>
                        <td>
                            @if($patient->birth_date)
                                <span class="badge-modern badge-active" style="background:rgba(255,255,255,0.05);">{{ $patient->age_label }}</span>
                                @if($patient->birth_date->format('m-d') === now()->format('m-d'))
                                    <span title="¡Hoy es su cumpleaños!" style="margin-left:4px; cursor:help;">🎂</span>
                                    @if($patient->phone)
                                        <a href="https://wa.me/52{{ preg_replace('/[^0-9]/', '', $patient->phone) }}?text={{ urlencode('¡Feliz cumpleaños ' . $patient->name . '! 🎂🎉') }}" target="_blank" class="btn-icon" title="Felicitar por WhatsApp" style="color:#25D366; display:inline-flex; width:28px; height:28px; font-size:12px; margin-left:6px; align-items:center; justify-content:center; border:1px solid rgba(37,211,102,0.2); background:rgba(37,211,102,0.05); border-radius:4px;"><i class="fab fa-whatsapp"></i></a>
                                    @endif
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
                        <td style="text-align:center; min-width:60px;">
                            <a href="{{ route('admin.patients.show', $patient) }}" class="btn-icon" title="Ver expediente"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->admin ? '7' : '6' }}">
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
        </div>{{-- end overflow-x --}}
    </div>{{-- end glass-card --}}

    {{-- ═══ MOBILE CARD VIEW (visible ≤ 768px) ═══ --}}
    <div class="patient-cards-mobile">
        @forelse ($patients as $patient)
        @php
            $isOwner = $patient->user_id === auth()->id();
            $isAdmin = auth()->user()->admin;
            $isCollaborator = !$isOwner && !$isAdmin;
        @endphp
        <div class="patient-mobile-card glass-card" wire:key="pm-{{ $patient->id }}">
            {{-- Top: avatar + name + status --}}
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--accent-gradient);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:white;flex-shrink:0;">
                    {{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->name), 0, 1)) }}{{ strtoupper(substr(\Illuminate\Support\Str::ascii($patient->last_name), 0, 1)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:700; font-size:15px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $patient->name }} {{ $patient->last_name }}
                        @if(!in_array($patient->status, ['activo','active']))
                            <span class="badge-modern badge-{{ $patient->status }}" style="font-size:9px; padding:2px 6px; margin-left:4px;">{{ $patient->status_label }}</span>
                        @endif
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $patient->expediente_id }}</div>
                </div>
                {{-- Action icons --}}
                <div style="display:flex; gap:6px; flex-shrink:0;">
                    <a href="{{ route('admin.patients.show', $patient) }}" class="btn-icon" title="Ver expediente"><i class="fas fa-eye"></i></a>
                </div>
            </div>
            {{-- Details grid --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:12px;">
                @if($patient->diagnosis)
                <div style="grid-column:1/-1; color:var(--text-secondary); background:var(--bg-glass); border-radius:6px; padding:6px 10px;">
                    <span style="color:var(--text-muted); font-size:10px;">PADECIMIENTO</span><br>
                    {{ \Illuminate\Support\Str::limit($patient->diagnosis, 60) }}
                </div>
                @endif
                <div style="background:var(--bg-glass); border-radius:6px; padding:6px 10px;">
                    <span style="color:var(--text-muted); font-size:10px;">EDAD</span><br>
                    {{ $patient->age_label ?? '—' }}
                </div>
                @if($isAdmin)
                <div style="background:var(--bg-glass); border-radius:6px; padding:6px 10px;">
                    <span style="color:var(--text-muted); font-size:10px;">FISIOTERAPEUTA</span><br>
                    {{ \Illuminate\Support\Str::limit($patient->user->name ?? '—', 20) }}
                </div>
                @endif
                @if($isOwner || $isAdmin)
                <div style="background:var(--bg-glass); border-radius:6px; padding:6px 10px;">
                    <span style="color:var(--text-muted); font-size:10px;">CONTACTO</span><br>
                    {{ $patient->phone ?? '—' }}
                </div>
                @endif
                <div style="background:var(--bg-glass); border-radius:6px; padding:6px 10px;">
                    <span style="color:var(--text-muted); font-size:10px;">REGISTRADO</span><br>
                    {{ $patient->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card" style="text-align:center; padding:40px;">
            <i class="fas fa-search-minus" style="font-size:36px; opacity:0.2; display:block; margin-bottom:12px;"></i>
            <p style="color:var(--text-secondary);">No se encontraron pacientes.</p>
        </div>
        @endforelse
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

        /* Desktop: show table, hide cards */
        .patient-table-desktop  { display: block; }
        .patient-cards-mobile   { display: none; }

        /* Mobile: hide table, show cards */
        @media (max-width: 768px) {
            .patient-table-desktop { display: none !important; }
            .patient-cards-mobile  { display: block; }
        }

        /* Card spacing */
        .patient-mobile-card {
            margin-bottom: 12px;
            padding: 16px;
        }
        .patient-mobile-card:last-child { margin-bottom: 0; }
    </style>

    {{-- Archivados / Papelera: esquina inferior derecha, al final de la lista --}}
    @if(auth()->user()->admin)
    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px; flex-wrap:wrap;">
        <a href="{{ route('admin.patients.archived') }}" class="btn-modern btn-secondary btn-sm" title="Pacientes archivados y solicitudes pendientes">
            <i class="fas fa-archive"></i> Archivados
        </a>
        <a href="{{ route('admin.patients.trashed') }}" class="btn-modern btn-secondary btn-sm" title="Papelera de reciclaje" style="color:#ef4444; border-color:rgba(239,68,68,0.3);">
            <i class="fas fa-trash"></i> Papelera
        </a>
    </div>
    @endif

</div>

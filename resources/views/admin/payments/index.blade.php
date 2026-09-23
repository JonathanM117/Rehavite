@extends('layouts.admin')

@section('title', 'Pagos')
@section('page-title', 'Pagos')

@section('content')
<div x-data="{ showForm: false }">

    <div class="flex-between mb-24" style="flex-wrap:wrap; gap:12px;">
        <form method="GET" action="{{ route('admin.payments.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <select name="patient_id" class="form-control-modern" style="max-width:220px; padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                <option value="">Todos los pacientes</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ (string) request('patient_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
                @endforeach
            </select>
            @if(auth()->user()->admin)
            <select name="therapist_id" class="form-control-modern" style="max-width:200px; padding:6px 12px; font-size:13px;" onchange="this.form.submit()">
                <option value="">Todos los fisios</option>
                @foreach($therapists as $t)
                    <option value="{{ $t->id }}" {{ (string) request('therapist_id') === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            @endif
            @if(request('patient_id') || request('therapist_id'))
                <a href="{{ route('admin.payments.index') }}" class="btn-modern btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Limpiar filtros
                </a>
            @endif
        </form>
        <button type="button" class="btn-modern btn-primary btn-sm" @click="showForm = !showForm">
            <i class="fas fa-plus"></i> Registrar Pago
        </button>
    </div>

    <div class="glass-card mb-24" x-show="showForm" x-cloak>
        <div style="font-weight:600;margin-bottom:16px;font-size:14px;">Registrar Nuevo Pago</div>
        <form action="{{ route('admin.payments.store') }}" method="POST">
            @csrf
            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Paciente *</label>
                    <select name="patient_id" class="form-control-modern" required>
                        <option value="">Seleccionar paciente...</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (string) request('patient_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha *</label>
                    <input type="date" name="date" class="form-control-modern" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Método de Pago</label>
                    <select name="payment_method" class="form-control-modern">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Concepto / Descripción</label>
                    <input type="text" name="concept" class="form-control-modern" placeholder="Ej: Pago mensual, Abono consulta 3...">
                </div>
                <div class="form-group">
                    <label class="form-label">Total a Pagar *</label>
                    <input type="number" name="total_amount" class="form-control-modern" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Importe Pagado *</label>
                    <input type="number" name="amount_paid" class="form-control-modern" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Terapeuta</label>
                <input type="text" name="therapist" class="form-control-modern" value="{{ auth()->user()->name }}">
            </div>
            <div class="form-group">
                <label class="form-label">Notas</label>
                <textarea name="notes" class="form-control-modern" rows="2" spellcheck="true"></textarea>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" class="btn-modern btn-secondary btn-sm" @click="showForm = false">Cancelar</button>
                <button type="submit" class="btn-modern btn-primary btn-sm"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>

    @if(session()->has('success'))
        <div style="background:rgba(39, 174, 96, 0.1); color:var(--success); padding:12px; border-radius:8px; margin-bottom:16px; font-size:13px; border:1px solid rgba(39, 174, 96, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:12px;">
        @forelse($payments as $pago)
            @php $remaining = $pago->remaining_balance; @endphp
            <div x-data="{ open: false, showAbono: false }" class="glass-card" style="padding:0; overflow:hidden;">
                <div @click="open = !open" style="padding:16px 20px; cursor:pointer; display:flex; align-items:center; gap:12px; justify-content:space-between; flex-wrap:wrap; user-select:none;">
                    <div style="flex:1; min-width:220px;">
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('admin.patients.show', $pago->patient_id) }}" onclick="event.stopPropagation()" style="font-weight:700; font-size:15px; color:var(--accent-primary); text-decoration:none;">
                                {{ $pago->patient->full_name ?? 'Paciente eliminado' }}
                            </a>
                            <span style="font-size:13px; color:var(--text-secondary);">{{ $pago->concept ?? 'Pago #' . $pago->id }}</span>
                            <span style="font-size:12px; color:var(--text-muted);">{{ $pago->date->format('d/m/Y') }}</span>
                            @if($remaining <= 0)
                                <span style="font-size:11px; background:rgba(39,174,96,0.15); color:var(--success); padding:2px 8px; border-radius:12px; font-weight:600;">✓ Saldado</span>
                            @else
                                <span style="font-size:11px; background:rgba(239,68,68,0.1); color:var(--danger); padding:2px 8px; border-radius:12px; font-weight:600;">Pendiente: ${{ number_format($remaining, 2) }}</span>
                            @endif
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                            ${{ number_format($pago->total_paid, 2) }} de ${{ number_format($pago->total_amount, 2) }}
                        </div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size:12px; color:var(--text-muted); transition:transform 0.2s;" :style="open ? 'transform:rotate(180deg)' : ''"></i>
                </div>

                <div x-show="open" x-collapse style="border-top:1px solid rgba(255,255,255,0.06); padding:16px 20px;">
                    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:12px; margin-bottom:16px;">
                        <div style="background:rgba(255,255,255,0.03); border-radius:8px; padding:10px;">
                            <div style="font-size:11px; color:var(--text-muted);">Total</div>
                            <div style="font-weight:700; font-size:16px;">${{ number_format($pago->total_amount, 2) }}</div>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); border-radius:8px; padding:10px;">
                            <div style="font-size:11px; color:var(--text-muted);">Pagado</div>
                            <div style="font-weight:700; color:var(--success);">${{ number_format($pago->total_paid, 2) }}</div>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); border-radius:8px; padding:10px;">
                            <div style="font-size:11px; color:var(--text-muted);">Saldo restante</div>
                            <div style="font-weight:700; color:{{ $remaining > 0 ? 'var(--danger)' : 'var(--success)' }}">${{ number_format($remaining, 2) }}</div>
                        </div>
                        <div style="background:rgba(255,255,255,0.03); border-radius:8px; padding:10px;">
                            <div style="font-size:11px; color:var(--text-muted);">Método</div>
                            <div style="font-weight:700;">{{ ucfirst($pago->payment_method ?? '—') }}</div>
                        </div>
                    </div>

                    @if($pago->installments->count() > 0)
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Historial de Abonos</div>
                            @foreach($pago->installments as $abono)
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:13px;">
                                    <div>
                                        <span style="color:var(--success); font-weight:600;">${{ number_format($abono->amount, 2) }}</span>
                                        <span style="color:var(--text-muted); margin-left:8px;">{{ $abono->date->format('d/m/Y') }}</span>
                                        @if($abono->payment_method)
                                            <span style="margin-left:6px; font-size:11px; color:var(--text-muted);">({{ ucfirst($abono->payment_method) }})</span>
                                        @endif
                                        @if($abono->user)
                                            <span style="margin-left:6px; font-size:11px; color:var(--text-muted);">— {{ $abono->user->name }}</span>
                                        @endif
                                    </div>
                                    @if($abono->notes)
                                        <span style="font-size:12px; color:var(--text-muted); font-style:italic;">{{ $abono->notes }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($remaining > 0)
                        <div style="margin-top:8px;">
                            <button type="button" @click="showAbono = !showAbono" class="btn-modern btn-sm" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.25); justify-content:center;">
                                <i class="fas fa-plus"></i> Agregar Abono
                            </button>
                            <div x-show="showAbono" x-cloak style="margin-top:12px; padding:16px; background:rgba(255,255,255,0.02); border-radius:8px; border:1px solid rgba(255,255,255,0.07);">
                                <form action="{{ route('admin.payments.installments.store', $pago->id) }}" method="POST">
                                    @csrf
                                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                                        <div class="form-group">
                                            <label class="form-label">Fecha *</label>
                                            <input type="date" name="date" class="form-control-modern" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Monto *</label>
                                            <input type="number" name="amount" class="form-control-modern" step="0.01" min="0.01" max="{{ $remaining }}" placeholder="0.00" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Método</label>
                                            <select name="payment_method" class="form-control-modern">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta">Tarjeta</option>
                                                <option value="transferencia">Transferencia</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom:12px;">
                                        <label class="form-label">Notas (opcional)</label>
                                        <input type="text" name="notes" class="form-control-modern" placeholder="Descripción del abono...">
                                    </div>
                                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                                        <button type="button" @click="showAbono = false" class="btn-modern btn-secondary btn-sm">Cancelar</button>
                                        <button type="submit" class="btn-modern btn-primary btn-sm"><i class="fas fa-save"></i> Registrar Abono</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--text-muted);">
                        <div>
                            @if($pago->therapist) <i class="fas fa-user-md"></i> {{ $pago->therapist }} @endif
                            @if($pago->notes) · <em>{{ $pago->notes }}</em> @endif
                        </div>
                        <form action="{{ route('admin.payments.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Eliminar este pago y todos sus abonos?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-modern btn-sm" style="background:rgba(220,53,69,0.1); color:var(--danger); border:1px solid rgba(220,53,69,0.2);" title="Eliminar pago">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state glass-card">
                <i class="fas fa-credit-card"></i>
                <p>No hay pagos registrados</p>
            </div>
        @endforelse
    </div>

    @if($payments->hasPages())
        <div class="pagination-modern" style="margin-top:24px;">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection

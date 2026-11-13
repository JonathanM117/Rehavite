@extends('layouts.admin')

@section('title', 'Nuevo Paciente')
@section('page-title', 'Registrar Nuevo Paciente')

@section('content')

    <div style="max-width:1100px; margin:0 auto;">
        <div class="flex-between" style="margin-bottom:16px;">
            <div></div>
            <button type="button" class="btn-modern btn-secondary btn-sm"
                    onclick="handleUnsavedExit('patient-create-form', () => { window.location.href = '{{ route('admin.patients.index') }}'; })">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>

        <form id="patient-create-form" action="{{ route('admin.patients.store') }}" method="POST"
              oninput="this.dataset.dirty = 'true'"
              onchange="this.dataset.dirty = 'true'"
              onsubmit="delete this.dataset.dirty">
            @csrf

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom:24px;">
                <button type="button" class="btn-modern btn-secondary"
                        onclick="handleUnsavedExit('patient-create-form', () => { window.location.href = '{{ route('admin.patients.index') }}'; })">
                    Cancelar
                </button>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save"></i> Registrar Paciente
                </button>
            </div>

            @include('livewire.partials.expediente-form-fields', ['patient' => null])

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;margin-bottom:40px;">
                <button type="button" class="btn-modern btn-secondary"
                        onclick="handleUnsavedExit('patient-create-form', () => { window.location.href = '{{ route('admin.patients.index') }}'; })">
                    Cancelar
                </button>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-save"></i> Registrar Paciente
                </button>
            </div>
        </form>
    </div>

@endsection

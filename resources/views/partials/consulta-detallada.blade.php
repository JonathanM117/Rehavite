{{-- ========================= --}}
{{-- ESTILO PARA TITULOS DE SECCIÓN --}}
{{-- ========================= --}}
<style>
    .consulta-wrapper {
        font-size: 1rem;
        line-height: 1.5;
    }

    .section-title {
        background: #e8f1ff;
        padding: 10px 14px;
        border-left: 4px solid #0d6efd;
        margin-top: 22px;
        margin-bottom: 10px;
        font-weight: bold;
        color: #0d6efd;
        border-radius: 4px;
    }

    .nota-card {
        background: #fafafa;
        border-radius: 6px;
        padding: 12px 14px;
        border: 1px solid #e0e0e0;
    }
</style>

<div class="consulta-wrapper">

    {{-- ========================= --}}
    {{-- MOTIVO DE CONSULTA --}}
    {{-- ========================= --}}
    <div class="section-title">
        <i class="fas fa-question-circle me-2"></i>
        Motivo de Consulta
    </div>

    <p class="mt-2">
        {{ $consulta->descripcion7 }}
    </p>



    {{-- ========================= --}}
    {{-- PADECIMIENTO ACTUAL --}}
    {{-- ========================= --}}
    <div class="section-title">
        <i class="fas fa-procedures me-2"></i>
        Padecimiento Actual
    </div>

    <div class="mt-2">

        <div class="row">
            <div class="col-md-6 mb-2">
                <strong>Inicio:</strong>
                <div>{{ $consulta->inicio8 }}</div>
            </div>

            <div class="col-md-6 mb-2">
                <strong>Mecanismo de lesión:</strong>
                <div>{{ $consulta->mecanismoLesion8 }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <strong>Evolución:</strong>
                <div>{{ $consulta->evolucion8 }}</div>
            </div>

            <div class="col-md-6 mb-2">
                <strong>Tratamiento actual:</strong>
                <div>{{ $consulta->tratamientoActual8 }}</div>
            </div>
        </div>

    </div>



    {{-- ========================= --}}
    {{-- NOTAS --}}
    {{-- ========================= --}}
    <div class="section-title mt-4">
        <i class="fas fa-notes-medical me-2"></i>
        Notas de Evolución
    </div>

    @if($consulta->notasClient->count() > 0)

        @foreach($consulta->notasClient as $nota)

            {{-- 🌟 Si NO está editando esta nota --}}
            @if($editingNoteId !== $nota->id)

                {{-- CARD DE NOTA EN MODO LECTURA --}}
                <div class="nota-card mb-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ $nota->fecha14 }} — Sesión {{ $nota->numSesion14 }}</strong>

                        <button class="btn btn-sm btn-primary"
                                wire:click="editNote({{ $nota->id }})">
                            Editar Nota
                        </button>
                    </div>

                    {{-- DESPLEGABLE --}}
                    <details class="mt-2">
                        <summary style="cursor:pointer; font-weight:bold; color:#0d6efd;">
                            Ver detalle completo
                        </summary>

                        <div class="mt-2">

                            <div class="mb-1"><strong>Descripción:</strong> {{ $nota->descripcion14 }}</div>

                            <div class="mb-1"><strong>Temperatura:</strong>
                                {{ $nota->temperaturaInicio14 }} → {{ $nota->temperaturaFinal14 }}
                            </div>

                            <div class="mb-1"><strong>Saturación:</strong>
                                {{ $nota->saturacionInicio14 }} → {{ $nota->saturacionFinal14 }}
                            </div>

                            <div class="mb-1"><strong>Tensión:</strong>
                                {{ $nota->tensionInicio14 }} → {{ $nota->tensionFinal14 }}
                            </div>

                            <div class="mb-1"><strong>Frecuencia:</strong>
                                {{ $nota->frecuenciaInicio14 }} → {{ $nota->frecuenciaFinal14 }}
                            </div>

                            <div class="mb-1"><strong>Plan:</strong> {{ $nota->plan14 }}</div>

                            <div class="mb-1"><strong>Tratamiento:</strong> {{ $nota->tratamiento14 }}</div>

                            <div class="mb-1"><strong>Notas adicionales:</strong> {{ $nota->notasAdicionales14 }}</div>

                        </div>
                    </details>

                </div>


            {{-- 🌟 MODO EDICIÓN DE NOTA --}}
            @else
                <form wire:submit.prevent="saveNote" class="mt-3">

                {{-- FECHA / SESIÓN --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fecha</label>
                        <input type="date" class="form-control" wire:model="editingNote.fecha14">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Número de Sesión</label>
                        <input type="text" class="form-control" wire:model="editingNote.numSesion14">
                    </div>
                </div>

                {{-- DESCRIPCIÓN GENERAL --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea class="form-control" rows="3" wire:model="editingNote.descripcion14"></textarea>
                </div>

                {{-- TEMPERATURAS --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Temperatura Inicio</label>
                        <input type="text" class="form-control" wire:model="editingNote.temperaturaInicio14">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Temperatura Final</label>
                        <input type="text" class="form-control" wire:model="editingNote.temperaturaFinal14">
                    </div>
                </div>

                {{-- SATURACIÓN --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Saturación Inicio</label>
                        <input type="text" class="form-control" wire:model="editingNote.saturacionInicio14">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Saturación Final</label>
                        <input type="text" class="form-control" wire:model="editingNote.saturacionFinal14">
                    </div>
                </div>

                {{-- TENSIÓN --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tensión Inicio</label>
                        <input type="text" class="form-control" wire:model="editingNote.tensionInicio14">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tensión Final</label>
                        <input type="text" class="form-control" wire:model="editingNote.tensionFinal14">
                    </div>
                </div>

                {{-- FRECUENCIA --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Frecuencia Inicio</label>
                        <input type="text" class="form-control" wire:model="editingNote.frecuenciaInicio14">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Frecuencia Final</label>
                        <input type="text" class="form-control" wire:model="editingNote.frecuenciaFinal14">
                    </div>
                </div>

                {{-- PLAN --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Plan</label>
                    <textarea class="form-control" rows="2" wire:model="editingNote.plan14"></textarea>
                </div>

                {{-- TRATAMIENTO --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tratamiento</label>
                    <textarea class="form-control" rows="2" wire:model="editingNote.tratamiento14"></textarea>
                </div>

                {{-- NOTAS ADICIONALES --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Notas Adicionales</label>
                    <textarea class="form-control" rows="2" wire:model="editingNote.notasAdicionales14"></textarea>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" wire:click="cancelEdit">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar Cambios
                    </button>
                </div>
            </form>

            @endif


        @endforeach

    @else
        <small class="text-muted">No hay notas registradas.</small>
    @endif


</div>

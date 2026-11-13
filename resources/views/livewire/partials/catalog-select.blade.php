{{--
    Select con catálogo fijo + "red de seguridad": si el valor actual (por
    ejemplo, texto libre heredado de antes del rediseño) no coincide con
    ninguna opción del catálogo, se agrega como una opción extra seleccionada
    para no ocultar/perder ese dato.

    Props esperadas ($attributes no se usa, son variables simples de Blade):
    - name: atributo name del <select>
    - options: array de strings con las opciones del catálogo
    - value: valor actual (old() ya resuelto)
    - label: etiqueta visible
    - placeholder: texto de la opción vacía (opcional)
    - required: bool (opcional)
--}}
@php
    $placeholder = $placeholder ?? 'Selecciona una opción';
    $required = $required ?? false;
    $legacyValue = ($value !== null && $value !== '' && !in_array($value, $options, true)) ? $value : null;
@endphp
<div class="campo {{ $wClass ?? 'w6' }}">
    <label class="form-label">{{ $label }}</label>
    <select name="{{ $name }}" @if(!empty($id)) id="{{ $id }}" @endif class="form-control-modern" {{ $required ? 'required' : '' }}>
        <option value="">{{ $placeholder }}</option>
        @if($legacyValue)
            <option value="{{ $legacyValue }}" selected>{{ $legacyValue }}</option>
        @endif
        @foreach($options as $option)
            <option value="{{ $option }}" {{ $value === $option ? 'selected' : '' }}>{{ $option }}</option>
        @endforeach
    </select>
</div>

@extends('layouts.admin')

@section('title', 'Pacientes')
@section('page-title', 'Directorio de Pacientes')

@section('content')
    <livewire:patient-index />
@endsection

@section('js')
<script>
    // Component is ready if any specific JS is needed
</script>
@endsection

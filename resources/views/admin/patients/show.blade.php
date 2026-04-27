@extends('layouts.admin')

@section('title', $patient->full_name)
@section('page-title', 'Expediente de Paciente')

@section('content')

    <livewire:patient-detail :patient="$patient" />

@endsection

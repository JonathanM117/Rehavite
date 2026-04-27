@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Mostrar detalle de Fisioterapeutas</h1>
@stop

@section('content')
    <p>Bienvenido Administrador.</p>

    <div class="card">
        <div class="card-body">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur id possimus tempora totam ab dolor nemo exercitationem non facilis laborum. Et illum recusandae veritatis quasi at nobis corrupti neque expedita!</p>
        </div>
    </div>
    @stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
    <script>
        Swal.fire(
        'Bienvenido Administrador!',
        'Prueba de SweetAlert2!',
        'success'
        )
    </script>
@stop
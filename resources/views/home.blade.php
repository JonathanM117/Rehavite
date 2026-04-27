@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Haz iniciado sesion!') }}
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="button" href="{{ url('/admin') }}">
                            <a href="{{ url('/admin') }}" style="text-decoration: none; color: white;">Ir a administrador</a>
                        </button>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

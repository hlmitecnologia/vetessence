@extends('layouts.adminlte')

@section('title', 'Mapa de Execução')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @livewire('execution-map-index')
            </div>
        </div>
    </div>
@endsection

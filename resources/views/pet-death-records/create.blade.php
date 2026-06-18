@extends('layouts.adminlte', ['title' => 'Novo Registro de Óbito'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('pet-death-record-form')
    </div>
</div>
@endsection

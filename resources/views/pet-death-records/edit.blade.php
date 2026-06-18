@extends('layouts.adminlte', ['title' => 'Editar Registro de Óbito'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('pet-death-record-form', ['id' => $petDeathRecord->id])
    </div>
</div>
@endsection

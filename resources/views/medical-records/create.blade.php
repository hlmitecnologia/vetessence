@extends('layouts.adminlte', ['title' => 'Novo Prontuário'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('medical-record-form', ['petId' => $selectedPet->id ?? null])
    </div>
</div>
@endsection

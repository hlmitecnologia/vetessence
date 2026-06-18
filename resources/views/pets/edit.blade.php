@extends('layouts.adminlte', ['title' => 'Editar Pet'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('pet-form', ['id' => $pet->id])
    </div>
</div>
@endsection

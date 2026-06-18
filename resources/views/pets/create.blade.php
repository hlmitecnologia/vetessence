@extends('layouts.adminlte', ['title' => 'Novo Pet'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('pet-form')
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Novo Prontuário'])

@section('header')
    <a href="{{ route('medical-records.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Registro de Prontuário</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    @livewire('medical-record-form', ['petId' => $selectedPet->id ?? null])
</div>
@endsection

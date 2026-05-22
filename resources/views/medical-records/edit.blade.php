@extends('layouts.adminlte', ['title' => 'Editar Prontuário'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @livewire('medical-record-form', ['recordId' => $medicalRecord->id])
    </div>
</div>
@endsection

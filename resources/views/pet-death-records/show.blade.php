@extends('layouts.adminlte', ['title' => 'Registro de Óbito'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Registro de Óbito</h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Pet</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->pet->name ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Data do Óbito</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->death_date->format('d/m/Y') }}</dd>
            <dt class="col-sm-3">Causa</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->cause ?? '-' }}</dd>
            <dt class="col-sm-3">Veterinário</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->attending_vet ?? '-' }}</dd>
            <dt class="col-sm-3">Destinação</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->disposition ?? '-' }}</dd>
            <dt class="col-sm-3">Observações</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->notes ?? '-' }}</dd>
            <dt class="col-sm-3">Registrado por</dt>
            <dd class="col-sm-9">{{ $petDeathRecord->registeredBy->name ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('pet-death-records.edit', $petDeathRecord) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('pet-death-records.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection

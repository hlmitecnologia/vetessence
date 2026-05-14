@extends('layouts.adminlte', ['title' => 'Padrão de Raça'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $breedDefault->breed }} ({{ $breedDefault->species }})</h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Espécie</dt>
            <dd class="col-sm-9">{{ $breedDefault->species }}</dd>
            <dt class="col-sm-3">Raça</dt>
            <dd class="col-sm-9">{{ $breedDefault->breed }}</dd>
            <dt class="col-sm-3">Porte</dt>
            <dd class="col-sm-9">{{ $breedDefault->size ?? '-' }}</dd>
            <dt class="col-sm-3">Peso Médio</dt>
            <dd class="col-sm-9">{{ $breedDefault->avg_weight_min && $breedDefault->avg_weight_max ? $breedDefault->avg_weight_min . ' - ' . $breedDefault->avg_weight_max . ' kg' : '-' }}</dd>
            <dt class="col-sm-3">Expectativa de Vida</dt>
            <dd class="col-sm-9">{{ $breedDefault->avg_lifespan_min && $breedDefault->avg_lifespan_max ? $breedDefault->avg_lifespan_min . ' - ' . $breedDefault->avg_lifespan_max . ' anos' : '-' }}</dd>
            <dt class="col-sm-3">Temperamento</dt>
            <dd class="col-sm-9">{{ $breedDefault->temperament ?? '-' }}</dd>
            <dt class="col-sm-3">Predisposições</dt>
            <dd class="col-sm-9">{{ $breedDefault->predispositions ?? '-' }}</dd>
            <dt class="col-sm-3">Observações</dt>
            <dd class="col-sm-9">{{ $breedDefault->notes ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('breed-defaults.edit', $breedDefault) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('breed-defaults.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection

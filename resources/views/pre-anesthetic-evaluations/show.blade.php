@extends('layouts.adminlte', ['title' => 'Avaliação Pré-Anestésica'])
@section('content')
    <div class="card"><div class="card-body">
        <p><strong>Pet:</strong> {{ $preAnestheticEvaluation->pet->name ?? '-' }}</p>
        <p><strong>ASA:</strong> {{ $preAnestheticEvaluation->asa_score }}</p>
        <p><strong>Status:</strong> {{ $preAnestheticEvaluation->status }}</p>
        <p><strong>Jejum:</strong> {{ $preAnestheticEvaluation->fasted ? 'Sim' : 'Não' }}</p>
        <p><strong>Hidratado:</strong> {{ $preAnestheticEvaluation->hydrated ? 'Sim' : 'Não' }}</p>
        <p><strong>Observações:</strong> {!! $preAnestheticEvaluation->observations !!}</p>
        <p><strong>Recomendações:</strong> {!! $preAnestheticEvaluation->recommendations !!}</p>
        <p><strong>Veterinário:</strong> {{ $preAnestheticEvaluation->vet->name ?? '-' }}</p>
        <a href="{{ route('pre-anesthetic-evaluations.edit', $preAnestheticEvaluation) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('pre-anesthetic-evaluations.index') }}" class="btn btn-secondary">Voltar</a>
    </div></div>
@endsection

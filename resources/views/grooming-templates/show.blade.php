@extends('layouts.adminlte', ['title' => 'Template de Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $groomingTemplate->name }}</h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nome</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->name }}</dd>
            <dt class="col-sm-3">Espécie</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->species ?? '-' }}</dd>
            <dt class="col-sm-3">Raça</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->breed ?? '-' }}</dd>
            <dt class="col-sm-3">Porte</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->size ?? '-' }}</dd>
            <dt class="col-sm-3">Serviços</dt>
            <dd class="col-sm-9">{{ is_array($groomingTemplate->services) ? implode(', ', $groomingTemplate->services) : '-' }}</dd>
            <dt class="col-sm-3">Preço</dt>
            <dd class="col-sm-9">R$ {{ number_format($groomingTemplate->price, 2, ',', '.') }}</dd>
            <dt class="col-sm-3">Duração</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->estimated_minutes }} min</dd>
            <dt class="col-sm-3">Ativo</dt>
            <dd class="col-sm-9">{!! $groomingTemplate->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</dd>
            <dt class="col-sm-3">Observações</dt>
            <dd class="col-sm-9">{{ $groomingTemplate->notes ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('grooming-templates.edit', $groomingTemplate) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('grooming-templates.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection

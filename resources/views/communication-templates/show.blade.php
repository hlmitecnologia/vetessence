@extends('layouts.adminlte', ['title' => 'Modelo de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $communicationTemplate->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('communication-templates.edit', $communicationTemplate) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('communication-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Nome:</strong>
                <p>{{ $communicationTemplate->name }}</p>
            </div>
            <div class="col-md-4">
                <strong>Tipo:</strong>
                <p>{{ $communicationTemplate->type ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Canal:</strong>
                <p>{{ strtoupper($communicationTemplate->channel ?? '-') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Ativo:</strong>
                <p>
                    @if($communicationTemplate->is_active)
                        <span class="badge badge-success">Sim</span>
                    @else
                        <span class="badge badge-secondary">Não</span>
                    @endif
                </p>
            </div>
        </div>
        @if($communicationTemplate->subject)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Assunto:</strong>
                <p>{{ $communicationTemplate->subject }}</p>
            </div>
        </div>
        @endif
        @if($communicationTemplate->content)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Conteúdo:</strong>
                <div class="p-3 bg-light rounded border">
                    {!! nl2br(e($communicationTemplate->content)) !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

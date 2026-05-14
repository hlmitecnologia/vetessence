@extends('layouts.adminlte', ['title' => 'Modelo de Termo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $consentTemplate->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('consent-templates.edit', $consentTemplate) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('consent-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Nome:</strong>
                <p>{{ $consentTemplate->name }}</p>
            </div>
            <div class="col-md-4">
                <strong>Slug:</strong>
                <p>{{ $consentTemplate->slug }}</p>
            </div>
            <div class="col-md-4">
                <strong>Categoria:</strong>
                <p>{{ $consentTemplate->category ?? '-' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Ativo:</strong>
                <p>
                    @if($consentTemplate->is_active)
                        <span class="badge badge-success">Sim</span>
                    @else
                        <span class="badge badge-secondary">Não</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <strong>Usado em:</strong>
                <p>{{ $consentTemplate->consentForms->count() }} termo(s)</p>
            </div>
        </div>
        @if($consentTemplate->description)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Descrição:</strong>
                <p>{{ $consentTemplate->description }}</p>
            </div>
        </div>
        @endif
        @if($consentTemplate->content)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Conteúdo:</strong>
                <div class="p-3 bg-light rounded border">
                    {!! nl2br(e($consentTemplate->content)) !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Interação Medicamentosa'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $drugInteraction->drug_a }} × {{ $drugInteraction->drug_b }}</h3>
        <div class="card-tools">
            <a href="{{ route('drug-interactions.edit', $drugInteraction) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('drug-interactions.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Medicamento A:</strong>
                <p class="h5">{{ $drugInteraction->drug_a }}</p>
            </div>
            <div class="col-md-4">
                <strong>Medicamento B:</strong>
                <p class="h5">{{ $drugInteraction->drug_b }}</p>
            </div>
            <div class="col-md-4">
                <strong>Severidade:</strong>
                <p>
                    @if($drugInteraction->severity == 'contraindicated')
                        <span class="badge badge-danger">Contraindicada</span>
                    @elseif($drugInteraction->severity == 'caution')
                        <span class="badge badge-warning">Precaução</span>
                    @else
                        <span class="badge badge-info">Menor</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <strong>Categoria:</strong>
                <p>{{ $drugInteraction->category ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Fonte:</strong>
                <p>{{ $drugInteraction->source ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Ativo:</strong>
                <p>
                    @if($drugInteraction->is_active)
                        <span class="badge badge-success">Sim</span>
                    @else
                        <span class="badge badge-secondary">Não</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Descrição:</strong>
                <div class="p-3 bg-light rounded border mt-1">
                    {{ $drugInteraction->description }}
                </div>
            </div>
        </div>
        @if($drugInteraction->mechanism)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Mecanismo:</strong>
                <p>{{ $drugInteraction->mechanism }}</p>
            </div>
        </div>
        @endif
        @if($drugInteraction->management)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Conduta / Manejo:</strong>
                <div class="p-3 bg-light rounded border mt-1">
                    {{ $drugInteraction->management }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

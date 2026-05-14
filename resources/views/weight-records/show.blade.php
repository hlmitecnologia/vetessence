@extends('layouts.adminlte', ['title' => 'Registro de Peso'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registro de Peso - {{ $weightRecord->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('weight-records.edit', $weightRecord) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('weight-records.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $weightRecord->pet->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Peso:</strong>
                <p class="h3 text-primary">{{ number_format($weightRecord->weight, 2, ',', '.') }} kg</p>
            </div>
            <div class="col-md-4">
                <strong>ECC:</strong>
                <p>{{ $weightRecord->bcs ? $weightRecord->bcs . '/9' : '-' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <strong>Data da Medição:</strong>
                <p>{{ $weightRecord->measurement_date->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Medido Por:</strong>
                <p>{{ $weightRecord->measuredBy->name ?? '-' }}</p>
            </div>
        </div>
        @if($weightRecord->notes)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Observações:</strong>
                <p>{{ $weightRecord->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

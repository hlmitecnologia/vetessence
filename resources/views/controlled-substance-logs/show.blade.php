@extends('layouts.adminlte', ['title' => 'Movimentação de Substância Controlada'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Movimentação - {{ $controlledSubstanceLog->substance->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substance-logs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Substância:</strong>
                <p>{{ $controlledSubstanceLog->substance->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Tipo:</strong>
                <p>
                    @if($controlledSubstanceLog->type === 'in')
                        <span class="badge badge-success">Entrada</span>
                    @else
                        <span class="badge badge-danger">Saída</span>
                    @endif
                </p>
            </div>
            <div class="col-md-4">
                <strong>Quantidade:</strong>
                <p class="h4">{{ number_format($controlledSubstanceLog->quantity, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <strong>Saldo Anterior:</strong>
                <p>{{ number_format($controlledSubstanceLog->balance_before, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Saldo Atual:</strong>
                <p>{{ number_format($controlledSubstanceLog->balance_after, 2, ',', '.') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Data:</strong>
                <p>{{ $controlledSubstanceLog->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <strong>Responsável:</strong>
                <p>{{ $controlledSubstanceLog->user->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Testemunha:</strong>
                <p>{{ $controlledSubstanceLog->witness->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $controlledSubstanceLog->pet->name ?? '-' }}</p>
            </div>
        </div>
        @if($controlledSubstanceLog->reason)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Motivo:</strong>
                <p>{{ $controlledSubstanceLog->reason }}</p>
            </div>
        </div>
        @endif
        @if($controlledSubstanceLog->notes)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Observações:</strong>
                <p>{{ $controlledSubstanceLog->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

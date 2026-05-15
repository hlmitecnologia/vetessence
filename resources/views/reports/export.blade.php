@extends('layouts.adminlte', ['title' => 'Exportar Relatório'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatório Exportado</h3>
    </div>
    <div class="card-body">
        <p>Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</p>
        <p>Total de faturas: {{ $invoices->count() }}</p>
    </div>
</div>
@endsection

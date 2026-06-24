@extends('layouts.adminlte', ['title' => 'Consumo de Pacotes'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Consumo de Pacotes</h3>
    </div>
    <div class="card-body">
        @if($consumptions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pet</th>
                    <th>Pacote</th>
                    <th>Serviço</th>
                    <th>Economia</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consumptions as $c)
                <tr>
                    <td>{{ $c->service_date->format('d/m/Y') }}</td>
                    <td>{{ $c->subscription->pet->name ?? '-' }}</td>
                    <td>{{ $c->subscription->package->name ?? '-' }}</td>
                    <td>{{ $c->service->name ?? '-' }}</td>
                    <td class="text-success">R$ {{ number_format($c->savings_amount, 2, ',', '.') }}</td>
                    <td>{{ $c->user->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum consumo registrado.</p>
        @endif
    </div>
</div>
@endsection

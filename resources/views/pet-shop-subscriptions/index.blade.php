@extends('layouts.adminlte', ['title' => 'Assinaturas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Assinaturas de Pacotes</h3>
        <div class="card-tools">
            <a href="{{ route('pet-shop-subscriptions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Assinatura
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($subscriptions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Pacote</th>
                    <th>Unidade</th>
                    <th>Início</th>
                    <th>Término</th>
                    <th>Usos</th>
                    <th>Economia</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $s)
                <tr>
                    <td>{{ $s->pet->name ?? '-' }}</td>
                    <td>{{ $s->package->name ?? '-' }}</td>
                    <td>{{ $s->branch->name ?? '-' }}</td>
                    <td data-order="{{ $s->start_date?->timestamp ?? 0 }}">{{ $s->start_date->format('d/m/Y') }}</td>
                    <td data-order="{{ $s->end_date?->timestamp ?? 0 }}">{{ $s->end_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $s->remaining_uses }}/{{ $s->total_uses }}</td>
                    <td class="text-success">R$ {{ number_format($s->total_savings, 2, ',', '.') }}</td>
                    <td>
                        @php
                            $statusClass = match($s->status) {
                                'active' => 'badge-success',
                                'expired' => 'badge-secondary',
                                'cancelled' => 'badge-danger',
                                'completed' => 'badge-info',
                                default => 'badge-warning',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('pet-shop-subscriptions.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        @if($s->status === 'active')
                        <form action="{{ route('pet-shop-subscriptions.cancel', $s) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger" data-confirm="Cancelar assinatura?"><i class="fas fa-ban"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma assinatura.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Exames'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Exames</h3>
        <div class="card-tools">
            <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($exams->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Veterinário</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td><strong>{{ $exam->pet->name ?? '-' }}</strong></td>
                    <td>{{ $exam->type }}</td>
                    <td>{{ $exam->vet->name ?? '-' }}</td>
                    <td>{{ $exam->requested_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'requested' => 'badge-warning',
                                'collected' => 'badge-info',
                                'analyzing' => 'badge-purple',
                                'ready' => 'badge-success',
                                'delivered' => 'badge-secondary',
                                'cancelled' => 'badge-danger'
                            ];
                            $statusLabels = [
                                'requested' => 'Solicitado',
                                'collected' => 'Coletado',
                                'analyzing' => 'Analisando',
                                'ready' => 'Pronto',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado'
                            ];
                        @endphp
                        <span class="badge {{ $statusColors[$exam->status] ?? 'badge-secondary' }}">
                            {{ $statusLabels[$exam->status] ?? $exam->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection
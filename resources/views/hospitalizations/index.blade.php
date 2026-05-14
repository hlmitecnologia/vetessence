@extends('layouts.adminlte', ['title' => 'Internações'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Internações</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalizations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Internação
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($hospitalizations->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Tutor</th>
                    <th>Data de Admissão</th>
                    <th>Departamento</th>
                    <th>Leito</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hospitalizations as $hospitalization)
                <tr>
                    <td><strong>{{ $hospitalization->pet->name ?? '-' }}</strong></td>
                    <td>{{ $hospitalization->tutor->name ?? '-' }}</td>
                    <td>{{ $hospitalization->admission_date->format('d/m/Y') }} {{ $hospitalization->admission_time ?? '' }}</td>
                    <td>{{ $hospitalization->department ?? '-' }}</td>
                    <td>{{ $hospitalization->bed ?? '-' }}</td>
                    <td>
                        @php
                            $statusColors = ['admitted' => 'badge-primary', 'discharged' => 'badge-success', 'transferred' => 'badge-warning'];
                            $statusLabels = ['admitted' => 'Internado', 'discharged' => 'Alta', 'transferred' => 'Transferido'];
                        @endphp
                        <span class="badge {{ $statusColors[$hospitalization->status] ?? 'badge-secondary' }}">
                            {{ $statusLabels[$hospitalization->status] ?? $hospitalization->status }}
                        </span>
                        @if($hospitalization->is_emergency)
                            <span class="badge badge-danger ml-1"><i class="fas fa-exclamation-triangle"></i> Emergência</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('hospitalizations.show', $hospitalization) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('hospitalizations.edit', $hospitalization) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('hospitalizations.destroy', $hospitalization) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma internação encontrada.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Vacinas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Vacinas</h3>
        <div class="card-tools">
            <a href="{{ route('vaccinations.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($vaccinations->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Vacina</th>
                    <th>Data</th>
                    <th>Próxima</th>
                    <th>Veterinário</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vaccinations as $vac)
                <tr>
                    <td><strong>{{ $vac->pet->name ?? '-' }}</strong></td>
                    <td>{{ $vac->vaccine }}</td>
                    <td>{{ $vac->date->format('d/m/Y') }}</td>
                    <td>{{ $vac->next_date ? $vac->next_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $vac->vet->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('vaccinations.show', $vac) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('vaccinations.edit', $vac) }}" class="btn btn-action btn-primary" title="Editar">
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
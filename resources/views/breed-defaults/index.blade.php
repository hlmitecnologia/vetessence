@extends('layouts.adminlte', ['title' => 'Padrões de Raça'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Padrões de Raça</h3>
        <div class="card-tools">
            <a href="{{ route('breed-defaults.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Peso Médio</th>
                    <th>Expectativa de Vida</th>
                    <th>Temperamento</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($defaults as $d)
                <tr>
                    <td>{{ $d->species }}</td>
                    <td>{{ $d->breed }}</td>
                    <td>{{ $d->size ?? '-' }}</td>
                    <td>{{ $d->avg_weight_min && $d->avg_weight_max ? $d->avg_weight_min . ' - ' . $d->avg_weight_max . ' kg' : '-' }}</td>
                    <td>{{ $d->avg_lifespan_min && $d->avg_lifespan_max ? $d->avg_lifespan_min . ' - ' . $d->avg_lifespan_max . ' anos' : '-' }}</td>
                    <td>{{ $d->temperament ?? '-' }}</td>
                    <td>{!! $d->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('breed-defaults.show', $d) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('breed-defaults.edit', $d) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('breed-defaults.destroy', $d) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Nenhum padrão encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $defaults->links() }}</div>
</div>
@endsection

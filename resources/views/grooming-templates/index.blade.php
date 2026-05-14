@extends('layouts.adminlte', ['title' => 'Templates de Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Templates de Banho/Tosa</h3>
        <div class="card-tools">
            <a href="{{ route('grooming-templates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Preço</th>
                    <th>Duração</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->species ?? '-' }}</td>
                    <td>{{ $t->breed ?? '-' }}</td>
                    <td>{{ $t->size ?? '-' }}</td>
                    <td>R$ {{ number_format($t->price, 2, ',', '.') }}</td>
                    <td>{{ $t->estimated_minutes }} min</td>
                    <td>{!! $t->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('grooming-templates.show', $t) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('grooming-templates.edit', $t) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('grooming-templates.destroy', $t) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Nenhum template encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $templates->links() }}</div>
</div>
@endsection

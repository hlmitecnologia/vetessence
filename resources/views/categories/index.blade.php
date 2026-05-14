@extends('layouts.adminlte', ['title' => 'Categorias'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Categorias</h3>
        <div class="card-tools">
            <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($categories->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Pai</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td><strong>{{ $cat->name }}</strong></td>
                    <td>
                        @php $typeLabels = ['product' => 'Produto', 'service' => 'Serviço', 'vaccine' => 'Vacina']; @endphp
                        <span class="badge badge-secondary">{{ $typeLabels[$cat->type] ?? $cat->type }}</span>
                    </td>
                    <td>{{ $cat->parent->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $cat) }}" class="btn btn-action btn-primary" title="Editar">
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
@extends('layouts.adminlte', ['title' => 'Substâncias Controladas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Substâncias Controladas</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Substância
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($substances->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Princípio Ativo</th>
                    <th>Lista/Controle</th>
                    <th>Registro ANVISA</th>
                    <th>Estoque Atual</th>
                    <th>Estoque Mínimo</th>
                    <th>Status</th>
                    <th style="width: 140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($substances as $substance)
                <tr>
                    <td><strong>{{ $substance->name }}</strong></td>
                    <td>{{ $substance->active_ingredient ?? '-' }}</td>
                    <td>{{ $substance->schedule ?? '-' }}</td>
                    <td>{{ $substance->anvisa_register ?? '-' }}</td>
                    <td class="{{ $substance->current_stock <= $substance->min_stock ? 'text-danger font-weight-bold' : '' }}">
                        {{ number_format($substance->current_stock, 2, ',', '.') }} {{ $substance->unit }}
                    </td>
                    <td>{{ number_format($substance->min_stock, 2, ',', '.') }} {{ $substance->unit }}</td>
                    <td>
                        @if($substance->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-secondary">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('controlled-substances.show', $substance) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('controlled-substances.edit', $substance) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('controlled-substances.destroy', $substance) }}" method="POST" class="d-inline">
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
        <p class="text-center text-muted">Nenhuma substância controlada cadastrada.</p>
        @endif
    </div>
</div>
@endsection

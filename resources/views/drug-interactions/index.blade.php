@extends('layouts.adminlte', ['title' => 'Interações Medicamentosas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Interações Medicamentosas</h3>
        <div class="card-tools">
            <a href="{{ route('drug-interactions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Interação
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar medicamento..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="severity" class="form-control form-control-sm">
                    <option value="">Todas as severidades</option>
                    <option value="contraindicated" {{ request('severity') == 'contraindicated' ? 'selected' : '' }}>Contraindicada</option>
                    <option value="caution" {{ request('severity') == 'caution' ? 'selected' : '' }}>Precaução</option>
                    <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Menor</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-control form-control-sm">
                    <option value="">Ativo/Inativo</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('drug-interactions.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($interactions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicamento A</th>
                    <th>Medicamento B</th>
                    <th>Severidade</th>
                    <th>Categoria</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interactions as $interaction)
                <tr>
                    <td><strong>{{ $interaction->drug_a }}</strong></td>
                    <td><strong>{{ $interaction->drug_b }}</strong></td>
                    <td>
                        @if($interaction->severity == 'contraindicated')
                            <span class="badge badge-danger">Contraindicada</span>
                        @elseif($interaction->severity == 'caution')
                            <span class="badge badge-warning">Precaução</span>
                        @else
                            <span class="badge badge-info">Menor</span>
                        @endif
                    </td>
                    <td>{{ $interaction->category ?? '-' }}</td>
                    <td>
                        @if($interaction->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('drug-interactions.show', $interaction) }}" class="btn btn-action btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('drug-interactions.edit', $interaction) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('drug-interactions.destroy', $interaction) }}" method="POST" class="d-inline">
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
        <div class="mt-3">
            {{ $interactions->appends(request()->query())->links() }}
        </div>
        @else
        <p class="text-center text-muted my-4">Nenhuma interação medicamentosa cadastrada.</p>
        @endif
    </div>
</div>
@endsection

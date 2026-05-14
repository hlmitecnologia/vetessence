@extends('layouts.adminlte', ['title' => 'Protocolos de Vacinação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Protocolos de Vacinação</h3>
        <div class="card-tools">
            <a href="{{ route('vaccine-protocols.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <select name="species" class="form-control">
                    <option value="">Todas as espécies</option>
                    <option value="canine" {{ request('species') == 'canine' ? 'selected' : '' }}>Canina</option>
                    <option value="feline" {{ request('species') == 'feline' ? 'selected' : '' }}>Felina</option>
                    <option value="equine" {{ request('species') == 'equine' ? 'selected' : '' }}>Equina</option>
                    <option value="bovine" {{ request('species') == 'bovine' ? 'selected' : '' }}>Bovina</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="is_core" class="form-control">
                    <option value="">Todos</option>
                    <option value="1" {{ request('is_core') == '1' ? 'selected' : '' }}>Essenciais</option>
                    <option value="0" {{ request('is_core') == '0' ? 'selected' : '' }}>Não essenciais</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($protocols->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Espécie</th>
                    <th>Vacina</th>
                    <th>Idade Início</th>
                    <th>Idade Fim</th>
                    <th>Série</th>
                    <th>Reforço</th>
                    <th>Tipo</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($protocols as $p)
                <tr>
                    <td>@lang('species.' . $p->species)</td>
                    <td><strong>{{ $p->vaccine_name }}</strong></td>
                    <td>{{ $p->age_start_weeks ? $p->age_start_weeks . ' sem' : '-' }}</td>
                    <td>{{ $p->age_end_weeks ? $p->age_end_weeks . ' sem' : '-' }}</td>
                    <td>{{ $p->is_initial ? $p->dose_number . 'ª dose' : 'Reforço' }}</td>
                    <td>{{ $p->booster_interval_months ? $p->booster_interval_months . ' meses' : '-' }}</td>
                    <td>
                        @if($p->is_core)
                            <span class="badge badge-primary">Essencial</span>
                        @else
                            <span class="badge badge-secondary">Não essencial</span>
                        @endif
                    </td>
                    <td>
                        @if($p->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-danger">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('vaccine-protocols.show', $p) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('vaccine-protocols.edit', $p) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $protocols->links() }}</div>
        @else
        <p class="text-center text-muted">Nenhum protocolo encontrado.</p>
        @endif
    </div>
</div>
@endsection

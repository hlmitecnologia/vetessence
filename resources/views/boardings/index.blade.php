@extends('layouts.adminlte', ['title' => 'Hospedagem & Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hospedagem & Banho/Tosa</h3>
        <div class="card-tools">
            <a href="{{ route('boardings.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Check-in
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por pet...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todos os status</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Hospedado</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control form-control-sm">
                    <option value="">Todos os tipos</option>
                    <option value="boarding" {{ request('type') == 'boarding' ? 'selected' : '' }}>Hospedagem</option>
                    <option value="grooming" {{ request('type') == 'grooming' ? 'selected' : '' }}>Banho/Tosa</option>
                    <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Ambos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('boardings.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if($boardings->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Check-in</th>
                    <th>Check-out Previsto</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boardings as $boarding)
                <tr>
                    <td><strong>{{ $boarding->pet->name ?? 'N/A' }}</strong></td>
                    <td>
                        @if($boarding->type == 'boarding') Hospedagem
                        @elseif($boarding->type == 'grooming') Banho/Tosa
                        @else Ambos @endif
                    </td>
                    <td data-order="{{ $boarding->check_in_at->format('Y-m-d H:i') }}">{{ $boarding->check_in_at->format('d/m/Y H:i') }}</td>
                    <td data-order="{{ optional($boarding->expected_check_out)->format('Y-m-d') ?? '' }}">{{ optional($boarding->expected_check_out)->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        @if($boarding->status == 'checked_in')
                            <span class="badge badge-success">Hospedado</span>
                        @elseif($boarding->status == 'checked_out')
                            <span class="badge badge-secondary">Finalizado</span>
                        @else
                            <span class="badge badge-danger">Cancelado</span>
                        @endif
                    </td>
                    <td>R$ {{ number_format($boarding->total_amount, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('boardings.show', $boarding) }}" class="btn btn-action btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($boarding->status == 'checked_in')
                        <a href="{{ route('boardings.edit', $boarding) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @else
        <p class="text-center text-muted my-4">Nenhuma hospedagem encontrada.</p>
        @endif
    </div>
</div>
@endsection

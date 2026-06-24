@extends('layouts.adminlte', ['title' => 'Certificados Sanitários'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Certificados Sanitários</h3>
        <div class="card-tools">
            <a href="{{ route('health-certificates.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Emitido</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Vencido</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($certificates->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº Certificado</th>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Destino</th>
                    <th>Emissão</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th style="width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($certificates as $c)
                <tr>
                    <td><strong>{{ $c->certificate_number }}</strong></td>
                    <td>{{ $c->pet->name ?? '-' }}</td>
                    <td>
                        @php
                            $typeLabels = ['international' => 'Internacional', 'domestic' => 'Nacional', 'boarding' => 'Hospedagem', 'exhibition' => 'Exposição', 'other' => 'Outro'];
                        @endphp
                        {{ $typeLabels[$c->type] ?? $c->type }}
                    </td>
                    <td>{{ $c->destination ?? '-' }}</td>
                    <td>{{ $c->issue_date->format('d/m/Y') }}</td>
                    <td>{{ $c->expiration_date ? $c->expiration_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        @php
                            $statusLabels = ['draft' => 'Rascunho', 'issued' => 'Emitido', 'expired' => 'Vencido', 'cancelled' => 'Cancelado'];
                            $statusColors = ['draft' => 'secondary', 'issued' => 'success', 'expired' => 'danger', 'cancelled' => 'warning'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$c->status] ?? 'secondary' }}">{{ $statusLabels[$c->status] ?? $c->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('health-certificates.show', $c) }}" class="btn btn-action btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('health-certificates.pdf', $c) }}" class="btn btn-action btn-success" title="Download PDF"><i class="fas fa-file-pdf"></i></a>
                        <a href="{{ route('health-certificates.edit', $c) }}" class="btn btn-action btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-center text-muted">Nenhum certificado encontrado.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Serviços'])
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Serviços</h3>
        <div class="card-tools">
            <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($services->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Categoria</th>
                    <th>Preço Base</th>
                    <th>Preços por Espécie</th>
                    <th>Duração</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $svc)
                <tr>
                    <td><strong>{{ $svc->name }}</strong></td>
                    <td>{{ $svc->category->name ?? '-' }}</td>
                    <td>R$ {{ number_format($svc->price, 2, ',', '.') }}</td>
                    <td>
                        @if($svc->priceTiers->count() > 0)
                            @foreach($svc->priceTiers as $tier)
                                <span class="badge badge-info" title="{{ $tier->size ? $tier->size : '' }}">
                                    {{ $tier->species }}{{ $tier->size ? ' ('.$tier->size.')' : '' }}: R$ {{ number_format($tier->price, 2, ',', '.') }}
                                </span><br>
                            @endforeach
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $svc->duration ? $svc->duration . ' min' : '-' }}</td>
                    <td>
                        <a href="{{ route('services.show', $svc) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('services.edit', $svc) }}" class="btn btn-action btn-primary" title="Editar">
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

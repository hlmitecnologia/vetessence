@extends('layouts.adminlte', ['title' => 'Gateways de Pagamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gateways de Pagamento</h3>
        <div class="card-tools">
            <a href="{{ route('payment-gateways.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Gateway</a>
        </div>
    </div>
    <div class="card-body">
        @if($gateways->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Provedor</th>
                    <th>Canal</th>
                    <th>Ativo</th>
                    <th>Sandbox</th>
                    <th>Data</th>
                    <th style="width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gateways as $gateway)
                <tr class="{{ $gateway->is_active ? 'table-success' : '' }}">
                    <td><strong>{{ $gateway->name }}</strong></td>
                    <td>{{ strtoupper($gateway->provider) }}</td>
                    <td>
                        @if($gateway->channel === 'portal')
                            <span class="badge badge-info">Portal</span>
                        @elseif($gateway->channel === 'pdv')
                            <span class="badge badge-primary">PDV</span>
                        @else
                            <span class="badge badge-success">Ambos</span>
                        @endif
                    </td>
                    <td>@if($gateway->is_active) <span class="badge badge-success">Sim</span> @else <span class="badge badge-secondary">Não</span> @endif</td>
                    <td>@if($gateway->is_sandbox) <span class="badge badge-warning">Sim</span> @else <span class="badge badge-info">Não</span> @endif</td>
                    <td data-order="{{ $gateway->created_at->format('Y-m-d') }}">{{ $gateway->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('payment-gateways.show', $gateway) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('payment-gateways.edit', $gateway) }}" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('payment-gateways.destroy', $gateway) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-confirm="Excluir gateway?" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-center text-muted my-4">Nenhum gateway cadastrado.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Pacotes Petshop'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pacotes Petshop</h3>
        <div class="card-tools">
            <a href="{{ route('pet-shop-packages.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Pacote
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($packages->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Preço Total</th>
                    <th>Economia</th>
                    <th>Validade</th>
                    <th>Usos</th>
                    <th>Unidade</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packages as $p)
                <tr>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ ucfirst($p->type) }}</td>
                    <td>R$ {{ number_format($p->total_price, 2, ',', '.') }}</td>
                    <td class="text-success">R$ {{ number_format($p->original_price - $p->total_price, 2, ',', '.') }}</td>
                    <td>{{ $p->validity_days }} dias</td>
                    <td>{{ $p->max_uses }}</td>
                    <td>{{ $p->branch->name ?? '-' }}</td>
                    <td>{!! $p->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('pet-shop-packages.edit', $p) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('pet-shop-packages.destroy', $p) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" data-confirm="Tem certeza?"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum pacote cadastrado.</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Fornecedores'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Fornecedores</h3>
        <div class="card-tools">
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($suppliers->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $sup)
                <tr>
                    <td><strong>{{ $sup->name }}</strong></td>
                    <td>{{ $sup->cnpj ?? '-' }}</td>
                    <td>{{ $sup->phone ?? '-' }}</td>
                    <td>{{ $sup->email ?? '-' }}</td>
                    <td>
                        <a href="{{ route('suppliers.show', $sup) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('suppliers.edit', $sup) }}" class="btn btn-action btn-primary" title="Editar">
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
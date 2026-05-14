@extends('layouts.adminlte', ['title' => 'Convênios'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Convênios</h3>
        <div class="card-tools">
            <a href="{{ route('convenios.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($convenios->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Plano</th>
                    <th>Desconto</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($convenios as $conv)
                <tr>
                    <td><strong>{{ $conv->name }}</strong></td>
                    <td>{{ $conv->plan_name ?? '-' }}</td>
                    <td>{{ $conv->discount_percent ? $conv->discount_percent . '%' : '-' }}</td>
                    <td>
                        @if($conv->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('convenios.show', $conv) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('convenios.edit', $conv) }}" class="btn btn-action btn-primary" title="Editar">
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
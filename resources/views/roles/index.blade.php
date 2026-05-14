@extends('layouts.adminlte', ['title' => 'Perfis'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perfis de Acesso</h3>
        <div class="card-tools">
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($roles->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Usuários</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td><strong>{{ $role->name }}</strong></td>
                    <td>{{ $role->description ?? 'Sem descrição' }}</td>
                    <td><span class="badge badge-primary">{{ $role->users_count ?? 0 }}</span></td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-action btn-primary" title="Editar">
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
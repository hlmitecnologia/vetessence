@extends('layouts.adminlte', ['title' => 'Unidade'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $branch->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('branches.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Nome:</strong><p>{{ $branch->name }}</p></div>
            <div class="col-md-4"><strong>Telefone:</strong><p>{{ $branch->phone ?? '-' }}</p></div>
            <div class="col-md-4"><strong>E-mail:</strong><p>{{ $branch->email ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Endereço:</strong><p>{{ $branch->address ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Cidade/UF:</strong><p>{{ $branch->city ?? '-' }}/{{ $branch->state ?? '' }}</p></div>
            <div class="col-md-3"><strong>CNPJ:</strong><p>{{ $branch->cnpj ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Principal:</strong><p>@if($branch->is_main) <span class="badge badge-primary">Sim</span> @else <span class="badge badge-secondary">Não</span> @endif</p></div>
            <div class="col-md-4"><strong>Ativa:</strong><p>@if($branch->is_active) <span class="badge badge-success">Sim</span> @else <span class="badge badge-secondary">Não</span> @endif</p></div>
            <div class="col-md-4"><strong>Funcionários:</strong><p>{{ $branch->users->count() }}</p></div>
        </div>
        @if($branch->users->count() > 0)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Funcionários:</strong>
                <ul>
                    @foreach($branch->users as $user)
                    <li>{{ $user->name }} ({{ $user->roles->pluck('name')->implode(', ') ?: 'Sem perfil' }})</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

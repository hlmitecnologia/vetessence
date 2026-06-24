@extends('layouts.adminlte', ['title' => 'Unidades'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Unidades (Multifilial)</h3>
        <div class="card-tools">
            <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Unidade</a>
        </div>
    </div>
    <div class="card-body">
        @if($branches->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cidade/UF</th>
                    <th>Telefone</th>
                    <th>Principal</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr class="{{ $branch->is_main ? 'table-success' : '' }}">
                    <td><strong>{{ $branch->name }}</strong></td>
                    <td>{{ $branch->city ?? '-' }}/{{ $branch->state ?? '' }}</td>
                    <td>{{ $branch->phone ?? '-' }}</td>
                    <td>@if($branch->is_main) <span class="badge badge-primary">Sim</span> @endif</td>
                    <td>@if($branch->is_active) <span class="badge badge-success">Sim</span> @else <span class="badge badge-secondary">Não</span> @endif</td>
                    <td>
                        <a href="{{ route('branches.show', $branch) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @else
        <p class="text-center text-muted my-4">Nenhuma unidade cadastrada.</p>
        @endif
    </div>
</div>
@endsection

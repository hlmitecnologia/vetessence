@extends('layouts.adminlte', ['title' => 'Cargos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cargos</h3>
        <div class="card-tools">
            @can('positions.create')
            <a href="{{ route('positions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Cargo</a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($positions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Departamento</th>
                    <th>Descrição</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($positions as $position)
                <tr>
                    <td><strong>{{ $position->name }}</strong></td>
                    <td>{{ $position->department->name ?? '-' }}</td>
                    <td>{{ $position->description ?? '-' }}</td>
                    <td>
                        <a href="{{ route('positions.show', $position) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        @can('positions.edit')
                        <a href="{{ route('positions.edit', $position) }}" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $positions->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhum cargo cadastrado.</p>
        @endif
    </div>
</div>
@endsection

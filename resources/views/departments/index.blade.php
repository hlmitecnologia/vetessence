@extends('layouts.adminlte', ['title' => 'Departamentos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Departamentos</h3>
        <div class="card-tools">
            @can('departments.create')
            <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Departamento</a>
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
        @if($departments->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Cargos</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td><strong>{{ $department->name }}</strong></td>
                    <td>{{ $department->description ?? '-' }}</td>
                    <td>{{ $department->positions_count }}</td>
                    <td>
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        @can('departments.edit')
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $departments->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhum departamento cadastrado.</p>
        @endif
    </div>
</div>
@endsection

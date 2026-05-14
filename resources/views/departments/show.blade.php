@extends('layouts.adminlte', ['title' => $department->name])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $department->name }}</h3>
        <div class="card-tools">
            @can('departments.edit')
            <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            @endcan
            <a href="{{ route('departments.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12"><strong>Nome:</strong><p>{{ $department->name }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12"><strong>Descrição:</strong><p>{{ $department->description ?? '-' }}</p></div>
        </div>
        @if($department->positions->count() > 0)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Cargos:</strong>
                <ul>
                    @foreach($department->positions as $position)
                    <li>{{ $position->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

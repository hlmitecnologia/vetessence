@extends('layouts.adminlte', ['title' => $position->name])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $position->name }}</h3>
        <div class="card-tools">
            @can('positions.edit')
            <a href="{{ route('positions.edit', $position) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            @endcan
            <a href="{{ route('positions.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6"><strong>Nome:</strong><p>{{ $position->name }}</p></div>
            <div class="col-md-6"><strong>Departamento:</strong><p>{{ $position->department->name ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12"><strong>Descrição:</strong><p>{{ $position->description ?? '-' }}</p></div>
        </div>
        @if($position->users->count() > 0)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Ocupantes:</strong>
                <ul>
                    @foreach($position->users as $user)
                    <li>{{ $user->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => $category->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Tipo</small>
                        <p>{{ ucfirst($category->type) }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Categoria Pai</small>
                        <p>{{ $category->parent->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection

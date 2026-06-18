@extends('layouts.adminlte', ['title' => 'Editar Perfil'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="name" value="{{ $role->name }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" value="{{ $role->slug }}" readonly class="form-control bg-light">
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="description" rows="2" class="wysiwyg form-control @error('description') is-invalid @enderror">{!! $role->description !!}</textarea>
                        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

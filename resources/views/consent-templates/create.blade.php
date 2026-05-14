@extends('layouts.adminlte', ['title' => 'Novo Modelo de Termo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Modelo de Termo</h3>
        <div class="card-tools">
            <a href="{{ route('consent-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('consent-templates.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome do Modelo *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                            <option value="">Selecione</option>
                            @foreach(['Cirurgia', 'Procedimento', 'Anestesia', 'Internação', 'Exame', 'Eutanásia', 'Vacina', 'Outros'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="content">Conteúdo do Termo *</label>
                <textarea name="content" id="content" rows="12" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                <small class="text-muted">
                    Use variáveis como {{ '{{' }}pet_name{{ '}}' }}, {{ '{{' }}tutor_name{{ '}}' }}, {{ '{{' }}vet_name{{ '}}' }}, {{ '{{' }}date{{ '}}' }} que serão substituídas automaticamente.
                </small>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Ativo</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection

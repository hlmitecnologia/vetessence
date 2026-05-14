@extends('layouts.adminlte', ['title' => 'Editar Modelo de Laudo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Modelo - {{ $clinicalReportTemplate->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('clinical-report-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('clinical-report-templates.update', $clinicalReportTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome do Modelo *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $clinicalReportTemplate->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="species">Espécie</label>
                        <select name="species" id="species" class="form-control @error('species') is-invalid @enderror">
                            <option value="">Todas as espécies</option>
                            <option value="canine" {{ old('species', $clinicalReportTemplate->species) == 'canine' ? 'selected' : '' }}>Canina</option>
                            <option value="feline" {{ old('species', $clinicalReportTemplate->species) == 'feline' ? 'selected' : '' }}>Felina</option>
                            <option value="equine" {{ old('species', $clinicalReportTemplate->species) == 'equine' ? 'selected' : '' }}>Equina</option>
                            <option value="bovine" {{ old('species', $clinicalReportTemplate->species) == 'bovine' ? 'selected' : '' }}>Bovina</option>
                            <option value="other" {{ old('species', $clinicalReportTemplate->species) == 'other' ? 'selected' : '' }}>Outras</option>
                        </select>
                        @error('species')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="specialty">Especialidade</label>
                        <select name="specialty" id="specialty" class="form-control @error('specialty') is-invalid @enderror">
                            <option value="">Selecione</option>
                            @foreach(['Clínica Geral', 'Cardiologia', 'Dermatologia', 'Oftalmologia', 'Ortopedia', 'Neurologia', 'Oncologia', 'Endocrinologia', 'Nefrologia', 'Gastroenterologia', 'Odontologia', 'Infectologia', 'Emergência', 'Imagem', 'Outros'] as $spec)
                                <option value="{{ $spec }}" {{ old('specialty', $clinicalReportTemplate->specialty) == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                            @endforeach
                        </select>
                        @error('specialty')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                            <option value="">Selecione</option>
                            @foreach(['SOAP', 'Anamnese', 'Exame Físico', 'Avaliação', 'Evolução', 'Alta', 'Encaminhamento', 'Relatório', 'Outros'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $clinicalReportTemplate->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="slug">Slug (URL amigável)</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $clinicalReportTemplate->slug) }}">
                        @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $clinicalReportTemplate->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="content">Conteúdo do Modelo *</label>
                <textarea name="content" id="content" rows="15" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $clinicalReportTemplate->content) }}</textarea>
                <small class="text-muted">
                    Use variáveis como {{ '{{' }}pet_name{{ '}}' }}, {{ '{{' }}tutor_name{{ '}}' }}, {{ '{{' }}vet_name{{ '}}' }}, {{ '{{' }}date{{ '}}' }}, {{ '{{' }}species{{ '}}' }}, {{ '{{' }}breed{{ '}}' }}, {{ '{{' }}age{{ '}}' }} que serão substituídas automaticamente ao gerar o laudo.
                </small>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $clinicalReportTemplate->is_active) ? 'checked' : '' }}>
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

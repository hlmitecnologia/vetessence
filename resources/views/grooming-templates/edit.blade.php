@extends('layouts.adminlte', ['title' => 'Editar Template de Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Template</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('grooming-templates.update', $groomingTemplate) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="name" class="form-control" value="{{ $groomingTemplate->name }}" required>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Espécie</label>
                        <select name="species" class="form-control">
                            <option value="">Todas</option>
                            <option value="canino" {{ $groomingTemplate->species == 'canino' ? 'selected' : '' }}>Canino</option>
                            <option value="felino" {{ $groomingTemplate->species == 'felino' ? 'selected' : '' }}>Felino</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Raça</label>
                        <input type="text" name="breed" class="form-control" value="{{ $groomingTemplate->breed }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Porte</label>
                        <select name="size" class="form-control">
                            <option value="">Todos</option>
                            <option value="pequeno" {{ $groomingTemplate->size == 'pequeno' ? 'selected' : '' }}>Pequeno</option>
                            <option value="medio" {{ $groomingTemplate->size == 'medio' ? 'selected' : '' }}>Médio</option>
                            <option value="grande" {{ $groomingTemplate->size == 'grande' ? 'selected' : '' }}>Grande</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Serviços (JSON)</label>
                <textarea name="services" class="form-control" rows="3">{{ is_array($groomingTemplate->services) ? json_encode($groomingTemplate->services) : $groomingTemplate->services }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Preço *</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ $groomingTemplate->price }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Duração (min) *</label>
                        <input type="number" name="estimated_minutes" class="form-control" value="{{ $groomingTemplate->estimated_minutes }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ $groomingTemplate->is_active ? 'selected' : '' }}>Sim</option>
                            <option value="0" {{ !$groomingTemplate->is_active ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3">{{ $groomingTemplate->notes }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection

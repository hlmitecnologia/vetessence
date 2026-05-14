@extends('layouts.adminlte', ['title' => 'Novo Template de Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Novo Template</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('grooming-templates.store') }}">
            @csrf
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Espécie</label>
                        <select name="species" class="form-control">
                            <option value="">Todas</option>
                            <option value="canino">Canino</option>
                            <option value="felino">Felino</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Raça</label>
                        <input type="text" name="breed" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Porte</label>
                        <select name="size" class="form-control">
                            <option value="">Todos</option>
                            <option value="pequeno">Pequeno</option>
                            <option value="medio">Médio</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Serviços (JSON)</label>
                <textarea name="services" class="form-control" rows="3" placeholder='["banho", "tosa", "hidratação"]'></textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Preço *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Duração (min) *</label>
                        <input type="number" name="estimated_minutes" class="form-control" value="60" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Ativo</label>
                        <select name="is_active" class="form-control">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection

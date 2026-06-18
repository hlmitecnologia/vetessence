@extends('layouts.adminlte', ['title' => 'Novo Plano Alimentar'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('diet-plans.store') }}">@csrf
            <div class="form-group"><label>Pet</label><x-tom-select name="pet_id" :value="old('pet_id')" required>
                @foreach($pets as $pet)<option value="{{ $pet->id }}">{{ $pet->name }}</option>@endforeach
            </x-tom-select></div>
            <div class="form-group"><label>Tipo de Dieta</label>
                <select name="diet_type" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="renal">Renal</option><option value="hepatic">Hepático</option>
                    <option value="urinary">Urinário</option><option value="hypoallergenic">Hipoalergênico</option>
                    <option value="weight_management">Controle de Peso</option><option value="gastrointestinal">Gastrointestinal</option>
                </select></div>
            <div class="form-group"><label>Marca</label><input name="brand" class="form-control"></div>
            <div class="form-group"><label>Produto</label><input name="product_name" class="form-control"></div>
            <div class="form-group"><label>Quantidade Diária</label><input name="daily_amount" class="form-control" placeholder="Ex: 100g, 1 sachê"></div>
            <div class="form-group"><label>Duração (dias)</label><input name="duration_days" type="number" class="form-control"></div>
            <div class="wysiwyg form-group"><label>Instruções</label><textarea name="instructions" class="wysiwyg form-control @error('instructions') is-invalid @enderror"></textarea>
            @error('instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div></div>
@endsection

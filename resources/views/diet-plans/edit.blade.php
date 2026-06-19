@extends('layouts.adminlte', ['title' => 'Editar Plano Alimentar'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('diet-plans.update', $dietPlan) }}">@csrf @method('PUT')
            <div class="form-group"><label>Pet</label><x-tom-select name="pet_id" :value="old('pet_id', $dietPlan->pet_id)" required>
                @foreach($pets as $pet)<option value="{{ $pet->id }}" {{ old('pet_id', $dietPlan->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>@endforeach
            </x-tom-select></div>
            <div class="form-group"><label>Tipo de Dieta</label>
                <select name="diet_type" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="renal" {{ old('diet_type', $dietPlan->diet_type) == 'renal' ? 'selected' : '' }}>Renal</option>
                    <option value="hepatic" {{ old('diet_type', $dietPlan->diet_type) == 'hepatic' ? 'selected' : '' }}>Hepático</option>
                    <option value="urinary" {{ old('diet_type', $dietPlan->diet_type) == 'urinary' ? 'selected' : '' }}>Urinário</option>
                    <option value="hypoallergenic" {{ old('diet_type', $dietPlan->diet_type) == 'hypoallergenic' ? 'selected' : '' }}>Hipoalergênico</option>
                    <option value="weight_management" {{ old('diet_type', $dietPlan->diet_type) == 'weight_management' ? 'selected' : '' }}>Controle de Peso</option>
                    <option value="gastrointestinal" {{ old('diet_type', $dietPlan->diet_type) == 'gastrointestinal' ? 'selected' : '' }}>Gastrointestinal</option>
                </select></div>
            <div class="form-group"><label>Marca</label><input name="brand" class="form-control" value="{{ old('brand', $dietPlan->brand) }}"></div>
            <div class="form-group"><label>Produto</label><input name="product_name" class="form-control" value="{{ old('product_name', $dietPlan->product_name) }}"></div>
            <div class="form-group"><label>Quantidade Diária</label><input name="daily_amount" class="form-control" placeholder="Ex: 100g, 1 sachê" value="{{ old('daily_amount', $dietPlan->daily_amount) }}"></div>
            <div class="form-group"><label>Duração (dias)</label><input name="duration_days" type="number" class="form-control" value="{{ old('duration_days', $dietPlan->duration_days) }}"></div>
            <div class="wysiwyg form-group"><label>Instruções</label><textarea name="instructions" class="wysiwyg form-control @error('instructions') is-invalid @enderror">{{ old('instructions', $dietPlan->instructions) }}</textarea>
            @error('instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div></div>
@endsection

@extends('layouts.adminlte', ['title' => 'Novo Controle Parasitário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registrar Controle Parasitário</h3>
        <div class="card-tools">
            <a href="{{ route('parasite-controls.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('parasite-controls.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="product_name">Produto *</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" value="{{ old('product_name') }}" placeholder="Ex: NexGard, Revolution" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="flea" {{ old('type') == 'flea' ? 'selected' : '' }}>Pulga</option>
                            <option value="tick" {{ old('type') == 'tick' ? 'selected' : '' }}>Carrapato</option>
                            <option value="heartworm" {{ old('type') == 'heartworm' ? 'selected' : '' }}>Verme do Coração</option>
                            <option value="intestinal" {{ old('type') == 'intestinal' ? 'selected' : '' }}>Vermífugo</option>
                            <option value="combination" {{ old('type') == 'combination' ? 'selected' : '' }}>Combinado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="active_ingredient">Princípio Ativo</label>
                        <input type="text" name="active_ingredient" id="active_ingredient" class="form-control" value="{{ old('active_ingredient') }}" placeholder="Ex: Afoxolaner">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dose">Dosagem</label>
                        <input type="text" name="dose" id="dose" class="form-control" value="{{ old('dose') }}" placeholder="Ex: 10-25 mg">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="application_date">Data Aplicação *</label>
                        <input type="date" name="application_date" id="application_date" class="form-control" value="{{ old('application_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="next_due_date">Próxima Data</label>
                        <input type="date" name="next_due_date" id="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="batch">Lote</label>
                        <input type="text" name="batch" id="batch" class="form-control" value="{{ old('batch') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_id">Veterinário *</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection

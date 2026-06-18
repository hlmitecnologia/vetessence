@extends('layouts.adminlte', ['title' => 'Registrar Vacina'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('vaccinations.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pet *</label>
                        <select name="pet_id" required class="form-control">
                            <option value="">Selecione...</option>
                            @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPet->id ?? '') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vacina *</label>
                        <input type="text" name="vaccine" value="{{ old('vaccine') }}" required class="form-control" placeholder="Ex: V8, Antirrábica">
                    </div>
                    <div class="form-group">
                        <label>Produto (para baixa de estoque)</label>
                        <select name="product_id" class="form-control">
                            <option value="">— Sem produto —</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->sale_price }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} (estoque: {{ $p->stock }}) — R$ {{ number_format($p->sale_price, 2, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de Aplicação *</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próxima Dose</label>
                                <input type="date" name="next_date" value="{{ old('next_date') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lote</label>
                                <input type="text" name="batch" value="{{ old('batch') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fabricante</label>
                                <input type="text" name="manufacturer" value="{{ old('manufacturer') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Veterinário *</label>
                        <select name="vet_id" required class="form-control">
                            <option value="">Selecione...</option>
                            @foreach($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

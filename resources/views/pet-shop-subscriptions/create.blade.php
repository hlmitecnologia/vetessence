@extends('layouts.adminlte', ['title' => 'Nova Assinatura'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Assinatura de Pacote</h3>
        <div class="card-tools">
            <a href="{{ route('pet-shop-subscriptions.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('pet-shop-subscriptions.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pet *</label>
                        <select name="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </select>
                        @error('pet_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pacote *</label>
                        <select name="package_id" class="form-control @error('package_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }} (R$ {{ number_format($pkg->total_price, 2, ',', '.') }})</option>
                            @endforeach
                        </select>
                        @error('package_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unidade *</label>
                        <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(\App\Models\Branch::where('is_active', true)->get() as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Data de Início *</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="custom-control custom-switch mt-4">
                            <input type="checkbox" name="auto_renew" class="custom-control-input" id="autoRenew" value="1" {{ old('auto_renew') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="autoRenew">Renovação Automática</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Criar Assinatura</button>
        </div>
    </form>
</div>
@endsection

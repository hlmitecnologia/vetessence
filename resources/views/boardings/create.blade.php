@extends('layouts.adminlte', ['title' => 'Novo Check-in'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Check-in</h3>
        <div class="card-tools">
            <a href="{{ route('boardings.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('boardings.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} ({{ optional($pet->tutor)->name ?? 'Sem tutor' }})</option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="boarding" {{ old('type', 'boarding') == 'boarding' ? 'selected' : '' }}>Hospedagem</option>
                            <option value="grooming" {{ old('type') == 'grooming' ? 'selected' : '' }}>Banho/Tosa</option>
                            <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Ambos</option>
                        </select>
                        @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="check_in_at">Data/Hora Check-in *</label>
                        <input type="datetime-local" name="check_in_at" id="check_in_at" class="form-control @error('check_in_at') is-invalid @enderror" value="{{ old('check_in_at', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('check_in_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="expected_check_out">Check-out Previsto</label>
                        <input type="date" name="expected_check_out" id="expected_check_out" class="form-control @error('expected_check_out') is-invalid @enderror" value="{{ old('expected_check_out') }}">
                        @error('expected_check_out')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="daily_rate">Diária (R$) *</label>
                        <input type="number" step="0.01" min="0" name="daily_rate" id="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate', '0') }}" required>
                        @error('daily_rate')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="grooming_fee">Taxa Banho/Tosa (R$)</label>
                        <input type="number" step="0.01" min="0" name="grooming_fee" id="grooming_fee" class="form-control @error('grooming_fee') is-invalid @enderror" value="{{ old('grooming_fee', '0') }}">
                        @error('grooming_fee')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pickup_contact">Contato para Retirada</label>
                        <input type="text" name="pickup_contact" id="pickup_contact" class="form-control @error('pickup_contact') is-invalid @enderror" value="{{ old('pickup_contact') }}">
                        @error('pickup_contact')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reason">Motivo da Hospedagem</label>
                        <input type="text" name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason') }}">
                        @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="feeding_instructions">Instruções de Alimentação</label>
                <textarea name="feeding_instructions" id="feeding_instructions" rows="2" class="form-control @error('feeding_instructions') is-invalid @enderror">{{ old('feeding_instructions') }}</textarea>
                @error('feeding_instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="medication_instructions">Instruções de Medicação</label>
                <textarea name="medication_instructions" id="medication_instructions" rows="2" class="form-control @error('medication_instructions') is-invalid @enderror">{{ old('medication_instructions') }}</textarea>
                @error('medication_instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Check-in</button>
        </div>
    </form>
</div>
@endsection

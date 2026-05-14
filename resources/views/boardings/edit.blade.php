@extends('layouts.adminlte', ['title' => 'Editar Hospedagem'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Hospedagem - {{ $boarding->pet->name ?? 'N/A' }}</h3>
        <div class="card-tools">
            <a href="{{ route('boardings.show', $boarding) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('boardings.update', $boarding) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <select name="pet_id" id="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $boarding->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                        @error('pet_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="boarding" {{ old('type', $boarding->type) == 'boarding' ? 'selected' : '' }}>Hospedagem</option>
                            <option value="grooming" {{ old('type', $boarding->type) == 'grooming' ? 'selected' : '' }}>Banho/Tosa</option>
                            <option value="both" {{ old('type', $boarding->type) == 'both' ? 'selected' : '' }}>Ambos</option>
                        </select>
                        @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="check_in_at">Data/Hora Check-in *</label>
                        <input type="datetime-local" name="check_in_at" class="form-control @error('check_in_at') is-invalid @enderror" value="{{ old('check_in_at', $boarding->check_in_at->format('Y-m-d\TH:i')) }}" required>
                        @error('check_in_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="expected_check_out">Check-out Previsto</label>
                        <input type="date" name="expected_check_out" class="form-control" value="{{ old('expected_check_out', optional($boarding->expected_check_out)->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="daily_rate">Diária (R$) *</label>
                        <input type="number" step="0.01" min="0" name="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate', $boarding->daily_rate) }}" required>
                        @error('daily_rate')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="grooming_fee">Taxa Banho/Tosa (R$)</label>
                        <input type="number" step="0.01" min="0" name="grooming_fee" class="form-control" value="{{ old('grooming_fee', $boarding->grooming_fee) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pickup_contact">Contato para Retirada</label>
                        <input type="text" name="pickup_contact" class="form-control" value="{{ old('pickup_contact', $boarding->pickup_contact) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reason">Motivo</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason', $boarding->reason) }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="feeding_instructions">Instruções de Alimentação</label>
                <textarea name="feeding_instructions" rows="2" class="form-control">{{ old('feeding_instructions', $boarding->feeding_instructions) }}</textarea>
            </div>
            <div class="form-group">
                <label for="medication_instructions">Instruções de Medicação</label>
                <textarea name="medication_instructions" rows="2" class="form-control">{{ old('medication_instructions', $boarding->medication_instructions) }}</textarea>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $boarding->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection

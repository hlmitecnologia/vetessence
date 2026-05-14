@extends('layouts.adminlte', ['title' => 'Novo Lembrete de Vacina'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Lembrete de Vacina</h3>
        <div class="card-tools">
            <a href="{{ route('vaccination-reminders.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('vaccination-reminders.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <select name="pet_id" id="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                            <option value="">Selecione um pet</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vaccination_id">Vacina *</label>
                        <select name="vaccination_id" id="vaccination_id" class="form-control @error('vaccination_id') is-invalid @enderror" required>
                            <option value="">Selecione a vacina</option>
                            @foreach($pets as $pet)
                                @foreach($pet->vaccinations as $vac)
                                    <option value="{{ $vac->id }}" {{ old('vaccination_id') == $vac->id ? 'selected' : '' }} data-pet="{{ $pet->id }}">
                                        {{ $pet->name }} - {{ $vac->vaccine }} ({{ $vac->date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('vaccination_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="scheduled_date">Data Agendada *</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" class="form-control @error('scheduled_date') is-invalid @enderror" value="{{ old('scheduled_date', date('Y-m-d')) }}" required>
                        @error('scheduled_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="channel">Canal</label>
                        <select name="channel" id="channel" class="form-control">
                            <option value="">Selecione</option>
                            <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>E-mail</option>
                            <option value="sms" {{ old('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="whatsapp" {{ old('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Enviado</option>
                            <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Falhou</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
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

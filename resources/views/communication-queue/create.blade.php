@extends('layouts.adminlte', ['title' => 'Nova Mensagem na Fila'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Mensagem na Fila de Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-queue.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('communication-queue.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tutor_id">Tutor *</label>
                        <x-tom-select name="tutor_id" id="tutor_id" :value="old('tutor_id')" required>
                            @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                                    {{ $tutor->name }} - {{ $tutor->phone ?? $tutor->email ?? '' }}
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('tutor_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')">
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="template_id">Modelo de Mensagem</label>
                        <x-tom-select name="template_id" id="template_id" :value="old('template_id')">
                            @foreach($templates as $t)
                                <option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->channel }})
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('template_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="channel">Canal *</label>
                        <select name="channel" id="channel" class="form-control @error('channel') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'] as $val => $label)
                                <option value="{{ $val }}" {{ old('channel') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('channel')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="destination">Destino (telefone/e-mail) *</label>
                        <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" required>
                        @error('destination')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="scheduled_at">Agendar Para</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}">
                        <small class="text-muted">Deixe em branco para enviar imediatamente.</small>
                        @error('scheduled_at')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="message_content">Conteúdo da Mensagem *</label>
                <textarea name="message_content" id="message_content" rows="5" class="wysiwyg form-control @error('message_content') is-invalid @enderror">{{ old('message_content') }}</textarea>
                @error('message_content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Adicionar à Fila
            </button>
        </div>
    </form>
</div>
@endsection

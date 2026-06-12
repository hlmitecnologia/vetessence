@extends('layouts.adminlte', ['title' => 'Nova Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-queues.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('communication-queues.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tutor_id">Tutor *</label>
                        <select name="tutor_id" id="tutor_id" class="form-control @error('tutor_id') is-invalid @enderror" required>
                            <option value="">Selecione um tutor</option>
                            @foreach(\App\Models\Tutor::with('user')->get() as $tutor)
                                <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                            @endforeach
                        </select>
                        @error('tutor_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet</label>
                        <select name="pet_id" id="pet_id" class="form-control">
                            <option value="">Selecione um pet (opcional)</option>
                            @foreach(\App\Models\Pet::where('is_active', true)->orderBy('name')->get() as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="template_id">Modelo</label>
                        <select name="template_id" id="template_id" class="form-control">
                            <option value="">Selecione um modelo (opcional)</option>
                            @foreach(\App\Models\CommunicationTemplate::where('is_active', true)->get() as $template)
                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="channel">Canal *</label>
                        <select name="channel" id="channel" class="form-control @error('channel') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>E-mail</option>
                            <option value="sms" {{ old('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="whatsapp" {{ old('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('channel')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="destination">Destino *</label>
                        <input type="text" name="destination" id="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}" placeholder="E-mail ou telefone" required>
                        @error('destination')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="scheduled_at">Agendar para</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="message_content">Mensagem *</label>
                <textarea name="message_content" id="message_content" rows="4" class="wysiwyg form-control @error('message_content') is-invalid @enderror" required>{{ old('message_content') }}</textarea>
                @error('message_content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
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

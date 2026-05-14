@extends('layouts.adminlte', ['title' => 'Editar Modelo de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Modelo - {{ $template->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('communication-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('communication-templates.update', $template) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome do Modelo *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control" required>
                            @foreach(['reminder' => 'Lembrete', 'recall' => 'Rechamada', 'promotional' => 'Promocional', 'notification' => 'Notificação', 'other' => 'Outro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $template->type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="channel">Canal *</label>
                        <select name="channel" id="channel" class="form-control" required>
                            @foreach(['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'] as $val => $label)
                                <option value="{{ $val }}" {{ old('channel', $template->channel) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="subject">Assunto *</label>
                <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $template->subject) }}" required>
                @error('subject')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="content">Conteúdo da Mensagem *</label>
                <textarea name="content" id="content" rows="8" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $template->content) }}</textarea>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Ativo</label>
                </div>
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

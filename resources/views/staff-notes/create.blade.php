@extends('layouts.adminlte', ['title' => 'Nova Nota Interna'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Nota Interna</h3>
        <div class="card-tools">
            <a href="{{ route('staff-notes.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('staff-notes.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="priority">Prioridade *</label>
                        <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="assigned_to">Atribuir a</label>
                        <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror">
                            <option value="">Todos (comunicado geral)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                            <option value="">Selecione</option>
                            <option value="Geral" {{ old('category') == 'Geral' ? 'selected' : '' }}>Geral</option>
                            <option value="Lembrete" {{ old('category') == 'Lembrete' ? 'selected' : '' }}>Lembrete</option>
                            <option value="Urgência" {{ old('category') == 'Urgência' ? 'selected' : '' }}>Urgência</option>
                            <option value="Procedimento" {{ old('category') == 'Procedimento' ? 'selected' : '' }}>Procedimento</option>
                            <option value="Reunião" {{ old('category') == 'Reunião' ? 'selected' : '' }}>Reunião</option>
                            <option value="Financeiro" {{ old('category') == 'Financeiro' ? 'selected' : '' }}>Financeiro</option>
                            <option value="Outros" {{ old('category') == 'Outros' ? 'selected' : '' }}>Outros</option>
                        </select>
                        @error('category')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="content">Conteúdo *</label>
                <textarea name="content" id="content" rows="8" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                @error('content')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enviar</button>
        </div>
    </form>
</div>
@endsection

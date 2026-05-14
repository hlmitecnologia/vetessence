@extends('layouts.adminlte', ['title' => 'Editar Nota Interna'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Nota - {{ $staffNote->title }}</h3>
        <div class="card-tools">
            <a href="{{ route('staff-notes.show', $staffNote) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('staff-notes.update', $staffNote) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title">Título *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $staffNote->title) }}" required>
                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="priority">Prioridade *</label>
                        <select name="priority" class="form-control @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority', $staffNote->priority) == 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="normal" {{ old('priority', $staffNote->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority', $staffNote->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ old('priority', $staffNote->priority) == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="assigned_to">Atribuir a</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">Todos (comunicado geral)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $staffNote->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Geral" {{ old('category', $staffNote->category) == 'Geral' ? 'selected' : '' }}>Geral</option>
                            <option value="Lembrete" {{ old('category', $staffNote->category) == 'Lembrete' ? 'selected' : '' }}>Lembrete</option>
                            <option value="Urgência" {{ old('category', $staffNote->category) == 'Urgência' ? 'selected' : '' }}>Urgência</option>
                            <option value="Procedimento" {{ old('category', $staffNote->category) == 'Procedimento' ? 'selected' : '' }}>Procedimento</option>
                            <option value="Reunião" {{ old('category', $staffNote->category) == 'Reunião' ? 'selected' : '' }}>Reunião</option>
                            <option value="Financeiro" {{ old('category', $staffNote->category) == 'Financeiro' ? 'selected' : '' }}>Financeiro</option>
                            <option value="Outros" {{ old('category', $staffNote->category) == 'Outros' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="content">Conteúdo *</label>
                <textarea name="content" rows="8" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $staffNote->content) }}</textarea>
                @error('content')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection

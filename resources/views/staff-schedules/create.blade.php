@extends('layouts.adminlte', ['title' => 'Nova Escala'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Escala de Trabalho</h3>
        <div class="card-tools">
            <a href="{{ route('staff-schedules.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('staff-schedules.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="user_id">Funcionário *</label>
                <x-tom-select name="user_id" id="user_id" :value="old('user_id')" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </x-tom-select>
                @error('user_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="branch_id">Unidade *</label>
                <x-tom-select name="branch_id" id="branch_id" :value="old('branch_id')" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </x-tom-select>
                @error('branch_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="work_date">Data *</label>
                <input type="date" name="work_date" id="work_date" class="form-control @error('work_date') is-invalid @enderror" value="{{ old('work_date') }}" required>
                @error('work_date')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_time">Início *</label>
                        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_time">Término *</label>
                        <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="shift_type">Tipo de Turno *</label>
                <select name="shift_type" id="shift_type" class="form-control @error('shift_type') is-invalid @enderror" required>
                    <option value="">Selecione o tipo</option>
                    <option value="regular" {{ old('shift_type') == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="morning" {{ old('shift_type') == 'morning' ? 'selected' : '' }}>Manhã</option>
                    <option value="afternoon" {{ old('shift_type') == 'afternoon' ? 'selected' : '' }}>Tarde</option>
                    <option value="night" {{ old('shift_type') == 'night' ? 'selected' : '' }}>Noturno</option>
                </select>
                @error('shift_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_vet_shift" id="is_vet_shift" value="1" class="custom-control-input" {{ old('is_vet_shift') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_vet_shift">Disponível para agendamento online</label>
                </div>
                <small class="form-text text-muted">Quando marcado, o veterinário aparecerá no portal do tutor para agendamento de consultas.</small>
                @error('is_vet_shift')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_on_call" id="is_on_call" value="1" class="custom-control-input" {{ old('is_on_call') ? 'checked' : '' }} onchange="document.getElementById('on_call_type_group').style.display = this.checked ? '' : 'none'">
                    <label class="custom-control-label" for="is_on_call">Plantão</label>
                </div>
            </div>
            <div class="form-group" id="on_call_type_group" style="{{ old('is_on_call') ? '' : 'display:none' }}">
                <label for="on_call_type">Tipo de Plantão</label>
                <select name="on_call_type" id="on_call_type" class="form-control">
                    <option value="">Selecione</option>
                    <option value="sobreaviso" {{ old('on_call_type') == 'sobreaviso' ? 'selected' : '' }}>Sobreaviso</option>
                    <option value="presencial" {{ old('on_call_type') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="telefone" {{ old('on_call_type') == 'telefone' ? 'selected' : '' }}>Telefone</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
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

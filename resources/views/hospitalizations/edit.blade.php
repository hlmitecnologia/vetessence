@extends('layouts.adminlte', ['title' => 'Editar Internação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Internação - {{ $hospitalization->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalizations.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('hospitalizations.update', $hospitalization) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pet</label>
                        <p class="form-control-plaintext">{{ $hospitalization->pet->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tutor</label>
                        <p class="form-control-plaintext">{{ $hospitalization->tutor->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Veterinário Responsável</label>
                        <p class="form-control-plaintext">{{ $hospitalization->vet->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Data de Admissão</label>
                        <p class="form-control-plaintext">{{ $hospitalization->admission_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Horário</label>
                        <p class="form-control-plaintext">{{ $hospitalization->admission_time ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Departamento</label>
                        <p class="form-control-plaintext">{{ $hospitalization->department ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bed">Leito</label>
                        <input type="text" name="bed" id="bed" class="form-control @error('bed') is-invalid @enderror" value="{{ old('bed', $hospitalization->bed) }}">
                        @error('bed')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach(['admitted' => 'Internado', 'discharged' => 'Alta', 'transferred' => 'Transferido'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $hospitalization->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Emergência</label>
                <p class="form-control-plaintext">{{ $hospitalization->is_emergency ? 'Sim' : 'Não' }}</p>
            </div>
            <div class="form-group">
                <label>Motivo da Internação</label>
                <p class="form-control-plaintext">{!! $hospitalization->admission_reason !!}</p>
            </div>
            @if($hospitalization->initial_diagnosis)
            <div class="form-group">
                <label>Diagnóstico Inicial</label>
                <p class="form-control-plaintext">{!! $hospitalization->initial_diagnosis !!}</p>
            </div>
            @endif
            @if($hospitalization->status === 'discharged')
            <hr>
            <h5>Informações de Alta</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="discharged_at">Data da Alta</label>
                        <input type="date" name="discharged_at" id="discharged_at" class="form-control" value="{{ old('discharged_at', $hospitalization->discharged_at?->format('Y-m-d') ?? '') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="discharge_summary">Resumo de Alta</label>
                <textarea name="discharge_summary" id="discharge_summary" rows="3" class="wysiwyg form-control @error('discharge_summary') is-invalid @enderror">{{ old('discharge_summary', $hospitalization->discharge_summary) }}</textarea>
                            @error('discharge_summary')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="discharge_instructions">Instruções de Alta</label>
                <textarea name="discharge_instructions" id="discharge_instructions" rows="3" class="wysiwyg form-control @error('discharge_instructions') is-invalid @enderror">{{ old('discharge_instructions', $hospitalization->discharge_instructions) }}</textarea>
                            @error('discharge_instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            @endif
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection

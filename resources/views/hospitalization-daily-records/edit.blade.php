@extends('layouts.adminlte', ['title' => 'Editar Registro Diário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Registro Diário</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalization-daily-records.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('hospitalization-daily-records.update', $hospitalizationDailyRecord) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hospitalization_id">Internação *</label>
                        <select name="hospitalization_id" id="hospitalization_id" class="form-control @error('hospitalization_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($hospitalizations as $hosp)
                                <option value="{{ $hosp->id }}" {{ old('hospitalization_id', $hospitalizationDailyRecord->hospitalization_id) == $hosp->id ? 'selected' : '' }}>
                                    {{ $hosp->pet->name ?? '#' . $hosp->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('hospitalization_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            @include('hospitalizations._daily_record_form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection

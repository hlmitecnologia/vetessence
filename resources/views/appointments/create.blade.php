@extends('layouts.adminlte', ['title' => 'Nova Consulta'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('appointments.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pet *</label>
                                <x-tom-select name="pet_id" :value="old('pet_id')" required>
                                    @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                        {{ $pet->name }}
                                        @if($pet->tutors->first()) - {{ $pet->tutors->first()->name }} @endif
                                    </option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Veterinário *</label>
                                <x-tom-select name="vet_id" :value="old('vet_id')" required>
                                    @foreach($veterinarians as $vet)
                                    <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data *</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora *</label>
                                <input type="time" name="time" value="{{ old('time') }}" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo *</label>
                                <select name="type" required class="form-control">
                                    <option value="">Selecione...</option>
                                    <option value="consulta" {{ old('type') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                                    <option value="retorno" {{ old('type') == 'retorno' ? 'selected' : '' }}>Retorno</option>
                                    <option value="emergencia" {{ old('type') == 'emergencia' ? 'selected' : '' }}>Emergência</option>
                                    <option value="cirurgia" {{ old('type') == 'cirurgia' ? 'selected' : '' }}>Cirurgia</option>
                                    <option value="vacina" {{ old('type') == 'vacina' ? 'selected' : '' }}>Vacina</option>
                                    <option value="exame" {{ old('type') == 'exame' ? 'selected' : '' }}>Exame</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Motivo/Observações</label>
                                <textarea name="reason" rows="3" class="wysiwyg form-control @error('reason') is-invalid @enderror">{{ old('reason') }}</textarea>
                                @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <label>Serviços</label>
                            <div class="row">
                                @foreach($services as $service)
                                <div class="col-md-4 mb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" class="custom-control-input" id="svc-{{ $service->id }}">
                                        <label class="custom-control-label" for="svc-{{ $service->id }}">
                                            {{ $service->name }} <small class="text-muted">R$ {{ number_format($service->price, 2, ',', '.') }}</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Agendar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

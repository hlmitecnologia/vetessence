@extends('layouts.adminlte', ['title' => 'Editar Consulta'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('appointments.update', $appointment) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pet *</label>
                                <x-tom-select name="pet_id" :value="old('pet_id', $appointment->pet_id)" required>
                                    @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}" {{ $appointment->pet_id == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Veterinário *</label>
                                <x-tom-select name="vet_id" :value="old('vet_id', $appointment->vet_id)" required>
                                    @foreach($veterinarians as $vet)
                                    <option value="{{ $vet->id }}" {{ $appointment->vet_id == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data *</label>
                                <input type="date" name="date" value="{{ $appointment->date->format('Y-m-d') }}" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora *</label>
                                <input type="time" name="time" value="{{ $appointment->time instanceof \Carbon\Carbon ? $appointment->time->format('H:i') : substr($appointment->time, 0, 5) }}" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo *</label>
                                <select name="type" required class="form-control">
                                    @foreach(['consulta', 'retorno', 'emergencia', 'cirurgia', 'vacina', 'exame'] as $type)
                                    <option value="{{ $type }}" {{ $appointment->type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required class="form-control">
                                    @foreach(['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'] as $status)
                                    <option value="{{ $status }}" {{ $appointment->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Motivo</label>
                                <textarea name="reason" rows="3" class="wysiwyg form-control @error('reason') is-invalid @enderror">{!! $appointment->reason !!}</textarea>
                                @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

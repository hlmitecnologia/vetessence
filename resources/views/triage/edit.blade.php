@extends('layouts.adminlte', ['title' => 'Editar Triagem'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('triage.update', $triage) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pet</label>
                        <p class="form-control-plaintext font-weight-bold">{{ $triage->pet->name ?? '-' }}</p>
                    </div>
                    <div class="form-group">
                        <label>Severidade *</label>
                        <select name="severity" required class="form-control">
                            <option value="green" {{ $triage->severity == 'green' ? 'selected' : '' }}>Verde - Não urgente</option>
                            <option value="yellow" {{ $triage->severity == 'yellow' ? 'selected' : '' }}>Amarelo - Prioritário</option>
                            <option value="orange" {{ $triage->severity == 'orange' ? 'selected' : '' }}>Laranja - Urgência</option>
                            <option value="red" {{ $triage->severity == 'red' ? 'selected' : '' }}>Vermelho - Emergência</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required class="form-control">
                            <option value="waiting" {{ $triage->status == 'waiting' ? 'selected' : '' }}>Aguardando</option>
                            <option value="in_consultation" {{ $triage->status == 'in_consultation' ? 'selected' : '' }}>Em consulta</option>
                            <option value="seen" {{ $triage->status == 'seen' ? 'selected' : '' }}>Atendido</option>
                            <option value="discharged" {{ $triage->status == 'discharged' ? 'selected' : '' }}>Liberado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Queixa Principal</label>
                        <textarea name="chief_complaint" rows="3" class="wysiwyg form-control @error('chief_complaint') is-invalid @enderror">{!! $triage->chief_complaint !!}</textarea>
                        @error('chief_complaint')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Veterinário Responsável</label>
                        <x-tom-select name="assigned_vet_id" :value="$triage->assigned_vet_id">
                            <option value="">Selecione...</option>
                            @foreach($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ $triage->assigned_vet_id == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>

            @php $vs = $triage->vital_signs ?? []; @endphp
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sinais Vitais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Temperatura (ºC)</label>
                                <input type="text" name="vs_temperature" value="{{ old('vs_temperature', $vs['temperature'] ?? '') }}" class="form-control" placeholder="38.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Frequência Cardíaca (bpm)</label>
                                <input type="text" name="vs_heart_rate" value="{{ old('vs_heart_rate', $vs['heart_rate'] ?? '') }}" class="form-control" placeholder="120">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Frequência Respiratória (mrm)</label>
                                <input type="text" name="vs_respiratory_rate" value="{{ old('vs_respiratory_rate', $vs['respiratory_rate'] ?? '') }}" class="form-control" placeholder="30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="text" name="vs_weight" value="{{ old('vs_weight', $vs['weight'] ?? '') }}" class="form-control" placeholder="10.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Mucosas</label>
                                <input type="text" name="vs_mucosa" value="{{ old('vs_mucosa', $vs['mucosa'] ?? '') }}" class="form-control" placeholder="Normocoradas">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hidratação</label>
                                <input type="text" name="vs_hydration" value="{{ old('vs_hydration', $vs['hydration'] ?? '') }}" class="form-control" placeholder="Normal">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Linfonodos</label>
                                <input type="text" name="vs_lymph_nodes" value="{{ old('vs_lymph_nodes', $vs['lymph_nodes'] ?? '') }}" class="form-control" placeholder="Normais">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('triage.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

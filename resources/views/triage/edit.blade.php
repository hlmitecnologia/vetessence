@extends('layouts.adminlte', ['title' => 'Editar Triagem'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
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
                        <textarea name="chief_complaint" rows="3" class="form-control">{{ $triage->chief_complaint }}</textarea>
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
                    <div class="form-group">
                        <label>Sinais Vitais (JSON)</label>
                        <textarea name="vital_signs" rows="2" class="form-control font-monospace small">{{ is_array($triage->vital_signs) ? json_encode($triage->vital_signs) : $triage->vital_signs }}</textarea>
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

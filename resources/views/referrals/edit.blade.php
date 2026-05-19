@extends('layouts.adminlte', ['title' => 'Editar Encaminhamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Encaminhamento - {{ $referral->referral_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('referrals.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('referrals.update', $referral) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nº do Encaminhamento</label>
                        <input type="text" class="form-control" value="{{ $referral->referral_number }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="referring_clinic">Clínica de Origem</label>
                        <input type="text" name="referring_clinic" id="referring_clinic" class="form-control" value="{{ old('referring_clinic', $referral->referring_clinic) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiving_clinic">Clínica de Destino *</label>
                        <input type="text" name="receiving_clinic" id="receiving_clinic" class="form-control" value="{{ old('receiving_clinic', $referral->receiving_clinic) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiving_vet_id">Veterinário Destino</label>
                        <x-tom-select name="receiving_vet_id" id="receiving_vet_id" :value="old('receiving_vet_id', $referral->receiving_vet_id)">
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('receiving_vet_id', $referral->receiving_vet_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['sent' => 'Enviado', 'received' => 'Recebido', 'in_progress' => 'Em Atendimento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $referral->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="completed_at">Concluído em</label>
                        <input type="date" name="completed_at" id="completed_at" class="form-control" value="{{ old('completed_at', $referral->completed_at ? $referral->completed_at->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="reason">Motivo do Encaminhamento *</label>
                <textarea name="reason" id="reason" rows="3" class="form-control" required>{{ old('reason', $referral->reason) }}</textarea>
            </div>
            <div class="form-group">
                <label for="clinical_history">Histórico Clínico</label>
                <textarea name="clinical_history" id="clinical_history" rows="4" class="form-control">{{ old('clinical_history', $referral->clinical_history) }}</textarea>
            </div>
            <div class="form-group">
                <label for="requested_procedures">Procedimentos Solicitados</label>
                <textarea name="requested_procedures" id="requested_procedures" rows="3" class="form-control">{{ old('requested_procedures', $referral->requested_procedures) }}</textarea>
            </div>
            <div class="form-group">
                <label for="response_notes">Resposta do Destino</label>
                <textarea name="response_notes" id="response_notes" rows="3" class="form-control">{{ old('response_notes', $referral->response_notes) }}</textarea>
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

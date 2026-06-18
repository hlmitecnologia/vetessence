@extends('layouts.adminlte', ['title' => 'Cirurgia - ' . ($surgery->pet->name ?? '-')])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Pet</small>
                        <p class="font-weight-bold">{{ $surgery->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Cirurgião</small>
                        <p>{{ $surgery->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Data</small>
                        <p>{{ $surgery->scheduled_date->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Tipo</small>
                        <p>{{ $surgery->surgery_type }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Anestesia</small>
                        <p>{{ $surgery->anesthesia_type ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Duração</small>
                        <p>{{ $surgery->surgery_duration ? $surgery->surgery_duration . ' min' : '-' }}</p>
                    </div>
                </div>
                @if($surgery->diagnosis)
                <hr>
                <small class="text-muted text-uppercase">Diagnóstico Pré-op</small>
                <p>{!! $surgery->diagnosis !!}</p>
                @endif
                @if($surgery->post_op_notes)
                <small class="text-muted text-uppercase">Pós-operatório</small>
                <p class="bg-light p-3 rounded">{!! $surgery->post_op_notes !!}</p>
                @endif
                @if($surgery->surgery_notes)
                <small class="text-muted text-uppercase">Observações</small>
                <p>{!! $surgery->surgery_notes !!}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('surgeries.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('surgeries.edit', $surgery) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection

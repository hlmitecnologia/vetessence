@extends('layouts.adminlte', ['title' => 'Protocolo de Vacinação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $vaccineProtocol->vaccine_name }}</h3>
        <div class="card-tools">
            <a href="{{ route('vaccine-protocols.edit', $vaccineProtocol) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('vaccine-protocols.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Espécie:</strong>
                <p>@lang('species.' . $vaccineProtocol->species)</p>
            </div>
            <div class="col-md-4">
                <strong>Vacina:</strong>
                <p class="h4">{{ $vaccineProtocol->vaccine_name }}</p>
            </div>
            <div class="col-md-4">
                <strong>Tipo:</strong>
                <p>
                    @if($vaccineProtocol->is_core)
                        <span class="badge badge-primary">Essencial</span>
                    @else
                        <span class="badge badge-secondary">Não essencial</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3">
                <strong>Idade Início:</strong>
                <p>{{ $vaccineProtocol->age_start_weeks ? $vaccineProtocol->age_start_weeks . ' semanas' : 'Qualquer idade' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Idade Fim:</strong>
                <p>{{ $vaccineProtocol->age_end_weeks ? $vaccineProtocol->age_end_weeks . ' semanas' : 'Sem limite' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Série Inicial:</strong>
                <p>{{ $vaccineProtocol->is_initial ? 'Sim (' . $vaccineProtocol->dose_number . 'ª dose)' : 'Não' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Reforço:</strong>
                <p>{{ $vaccineProtocol->booster_interval_months ? 'A cada ' . $vaccineProtocol->booster_interval_months . ' meses' : 'Não se aplica' }}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <strong>Ativo:</strong>
                <p>
                    @if($vaccineProtocol->is_active)
                        <span class="badge badge-success">Sim</span>
                    @else
                        <span class="badge badge-danger">Não</span>
                    @endif
                </p>
            </div>
        </div>
        @if($vaccineProtocol->notes)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Observações:</strong>
                <p>{{ $vaccineProtocol->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

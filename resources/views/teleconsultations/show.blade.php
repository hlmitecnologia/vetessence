@extends('layouts.adminlte', ['title' => 'Teleconsulta'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $teleconsultation->room_name }}</h3>
        <div class="card-tools">
            @if($teleconsultation->status == 'scheduled')
            <a href="{{ route('teleconsultations.start', $teleconsultation) }}" class="btn btn-success btn-sm"><i class="fas fa-play"></i> Iniciar</a>
            <a href="{{ route('teleconsultations.edit', $teleconsultation) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            @endif
            @if($teleconsultation->status == 'active')
            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#endModal"><i class="fas fa-stop"></i> Encerrar</button>
            @endif
            <a href="{{ route('teleconsultations.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Sala:</strong><p>{{ $teleconsultation->room_name }}</p></div>
            <div class="col-md-3"><strong>Token:</strong><p><code>{{ $teleconsultation->room_token }}</code></p></div>
            <div class="col-md-3"><strong>Provedor:</strong><p>{{ strtoupper($teleconsultation->provider) }}</p></div>
            <div class="col-md-3"><strong>Status:</strong><p>
                @if($teleconsultation->status == 'scheduled') <span class="badge badge-info">Agendada</span>
                @elseif($teleconsultation->status == 'active') <span class="badge badge-success">Em Andamento</span>
                @elseif($teleconsultation->status == 'completed') <span class="badge badge-secondary">Concluída</span>
                @else <span class="badge badge-danger">Cancelada</span> @endif
            </p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3"><strong>Pet:</strong><p>{{ $teleconsultation->pet->name ?? 'N/A' }}</p></div>
            <div class="col-md-3"><strong>Veterinário:</strong><p>{{ $teleconsultation->vet->name ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Tutor:</strong><p>{{ $teleconsultation->tutor->name ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Agendado:</strong><p>{{ $teleconsultation->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</p></div>
        </div>
        @if($teleconsultation->status == 'active')
        <div class="row mt-3">
            <div class="col-md-12">
                <a href="{{ $teleconsultation->room_url }}" target="_blank" class="btn btn-lg btn-success">
                    <i class="fas fa-video"></i> Entrar na Sala
                </a>
                <small class="text-muted ml-2">{{ $teleconsultation->room_url }}</small>
            </div>
        </div>
        @endif
        @if($teleconsultation->started_at)
        <div class="row mt-2">
            <div class="col-md-4"><strong>Iniciado em:</strong><p>{{ $teleconsultation->started_at->format('d/m/Y H:i') }}</p></div>
            @if($teleconsultation->ended_at)
            <div class="col-md-4"><strong>Encerrado em:</strong><p>{{ $teleconsultation->ended_at->format('d/m/Y H:i') }}</p></div>
            <div class="col-md-4"><strong>Duração:</strong><p>{{ $teleconsultation->duration_minutes }} min</p></div>
            @endif
        </div>
        @endif
        @if($teleconsultation->notes)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Observações:</strong><p>{{ $teleconsultation->notes }}</p></div>
        </div>
        @endif
    </div>
</div>

@if($teleconsultation->status == 'active')
<div class="modal fade" id="endModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('teleconsultations.end', $teleconsultation) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Encerrar Teleconsulta</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Observações finais</label>
                        <textarea name="notes" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-stop"></i> Encerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

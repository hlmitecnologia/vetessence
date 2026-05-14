@extends('layouts.adminlte', ['title' => 'Sala de Teleconsulta'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $teleconsultation->room_name }}</h3>
        <div class="card-tools">
            <a href="{{ $teleconsultation->room_url }}" target="_blank" class="btn btn-success btn-sm"><i class="fas fa-external-link-alt"></i> Abrir em Nova Janela</a>
            <form action="{{ route('teleconsultations.end', $teleconsultation) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-stop"></i> Encerrar</button>
            </form>
            <a href="{{ route('teleconsultations.show', $teleconsultation) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Detalhes</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="embed-responsive embed-responsive-16by9" style="height: 70vh;">
            <iframe src="{{ $teleconsultation->room_url }}?config.startWithAudioMuted=1&config.startWithVideoMuted=1" 
                    allow="camera; microphone; fullscreen; display-capture" 
                    class="embed-responsive-item"></iframe>
        </div>
    </div>
</div>
@endsection

@extends('layouts.mobile', ['title' => 'Início'])
@section('content')
    <div class="row mb-3">
        <div class="col-6 mb-2">
            <div class="card p-3 text-center">
                <i class="fas fa-ambulance fa-2x text-warning"></i>
                <h5 class="mt-2">Triagem</h5>
                <a href="{{ url('/m/triage') }}" class="btn btn-sm btn-outline-primary btn-block">Abrir</a>
            </div>
        </div>
        <div class="col-6 mb-2">
            <div class="card p-3 text-center">
                <i class="fas fa-prescription-bottle fa-2x text-info"></i>
                <h5 class="mt-2">Receitas</h5>
                <a href="{{ url('/m/prescriptions') }}" class="btn btn-sm btn-outline-primary btn-block">Abrir</a>
            </div>
        </div>
        <div class="col-6 mb-2">
            <div class="card p-3 text-center">
                <i class="fas fa-notes-medical fa-2x text-success"></i>
                <h5 class="mt-2">Prontuários</h5>
                <a href="{{ url('/m/records') }}" class="btn btn-sm btn-outline-primary btn-block">Abrir</a>
            </div>
        </div>
        <div class="col-6 mb-2">
            <div class="card p-3 text-center">
                <i class="fas fa-calendar-check fa-2x text-primary"></i>
                <h5 class="mt-2">Agenda</h5>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary btn-block" target="_blank">Abrir</a>
            </div>
        </div>
    </div>
    <p class="text-muted text-center small">Toque em um módulo para acessar. Vets em atendimento externo.</p>
@endsection

@extends('layouts.adminlte', ['title' => 'Solicitação de Agendamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Solicitação #{{ $onlineBooking->id }} - {{ $onlineBooking->tutor_name }}</h3>
        <div class="card-tools">
            <a href="{{ route('online-bookings.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Tutor:</strong><p>{{ $onlineBooking->tutor_name }}<br>{{ $onlineBooking->tutor_email }}<br>{{ $onlineBooking->tutor_phone }}</p></div>
            <div class="col-md-4"><strong>Pet:</strong><p>{{ $onlineBooking->pet_name }}<br>{{ $onlineBooking->pet_species }} @if($onlineBooking->pet_breed)- {{ $onlineBooking->pet_breed }}@endif</p></div>
            <div class="col-md-4"><strong>Preferência:</strong><p>{{ $onlineBooking->preferred_date->format('d/m/Y') }} @if($onlineBooking->preferred_time)às {{ $onlineBooking->preferred_time }}@endif</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Motivo:</strong><p>{{ $onlineBooking->reason ?? '-' }}</p></div>
            <div class="col-md-6"><strong>Status:</strong><p>
                @if($onlineBooking->status == 'pending') <span class="badge badge-warning">Pendente</span>
                @elseif($onlineBooking->status == 'confirmed') <span class="badge badge-success">Confirmado</span>
                @elseif($onlineBooking->status == 'rejected') <span class="badge badge-danger">Rejeitado</span>
                @endif
            </p></div>
        </div>
        @if($onlineBooking->convertedAppointment)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Consulta gerada:</strong> <a href="{{ route('appointments.show', $onlineBooking->convertedAppointment) }}">Agendamento #{{ $onlineBooking->convertedAppointment->id }}</a></div>
        </div>
        @endif
        @if($onlineBooking->handledBy)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Processado por:</strong> {{ $onlineBooking->handledBy->name }} em {{ optional($onlineBooking->handled_at)->format('d/m/Y H:i') }}</div>
        </div>
        @endif
        @if($onlineBooking->staff_notes)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Anotações:</strong><p>{{ $onlineBooking->staff_notes }}</p></div>
        </div>
        @endif
    </div>
</div>

@if($onlineBooking->status == 'pending')
<div class="card">
    <div class="card-header"><h3 class="card-title">Processar Solicitação</h3></div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" id="processTab">
            <li class="nav-item"><a class="nav-link active" href="#confirm" data-toggle="tab">Confirmar</a></li>
            <li class="nav-item"><a class="nav-link" href="#reject" data-toggle="tab">Rejeitar</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="confirm">
                <form action="{{ route('online-bookings.confirm', $onlineBooking) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data da Consulta *</label>
                                <input type="date" name="appointment_date" class="form-control" value="{{ $onlineBooking->preferred_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Horário *</label>
                                <input type="time" name="appointment_time" class="form-control" value="{{ $onlineBooking->preferred_time ?? '09:00' }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Veterinário *</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">Selecione</option>
                                    @foreach(\App\Models\User::where('is_active', true)->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="form-control">{{ $onlineBooking->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Confirmar e Criar Consulta</button>
                </form>
            </div>
            <div class="tab-pane" id="reject">
                <form action="{{ route('online-bookings.reject', $onlineBooking) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Motivo da rejeição</label>
                        <textarea name="reason" rows="3" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Rejeitar Solicitação</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

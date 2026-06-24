@extends('layouts.adminlte', ['title' => 'Solicitações de Folga'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Solicitações de Folga</h3>
        <div class="card-tools">
            <a href="{{ route('staff-schedules.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Escalas
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTimeOff">
                <i class="fas fa-plus"></i> Nova Solicitação
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($timeOffs->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Funcionário</th>
                    <th>Início</th>
                    <th>Término</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th style="width: 200px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeOffs as $timeOff)
                <tr>
                    <td>{{ $timeOff->user->name ?? '-' }}</td>
                    <td data-order="{{ $timeOff->start_date->format('Y-m-d') }}">{{ $timeOff->start_date->format('d/m/Y') }}</td>
                    <td data-order="{{ $timeOff->end_date->format('Y-m-d') }}">{{ $timeOff->end_date->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($timeOff->type) }}</td>
                    <td>
                        @php
                            $statusColors = ['pending' => 'badge-warning', 'approved' => 'badge-success', 'rejected' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $statusColors[$timeOff->status] ?? 'badge-secondary' }}">
                            {{ $timeOff->status == 'pending' ? 'Pendente' : ($timeOff->status == 'approved' ? 'Aprovado' : 'Rejeitado') }}
                        </span>
                    </td>
                    <td>
                        @if($timeOff->status == 'pending' && $isAdmin)
                        <form action="{{ route('staff-time-off.approve', $timeOff) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action btn-success" title="Aprovar">
                                <i class="fas fa-check"></i> Aprovar
                            </button>
                        </form>
                        <form action="{{ route('staff-time-off.reject', $timeOff) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action btn-danger" title="Rejeitar">
                                <i class="fas fa-times"></i> Rejeitar
                            </button>
                        </form>
                        @else
                        <span class="text-muted">---</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma solicitação encontrada.</p>
        @endif
    </div>
</div>
<div class="modal fade" id="modalTimeOff" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('staff-schedules.time-off.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Solicitação de Folga</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Funcionário *</label>
                        @if($isAdmin)
                        <select name="user_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <p class="form-control-plaintext">{{ auth()->user()->name }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Tipo *</label>
                        <select name="type" class="form-control" required>
                            <option value="">Selecione...</option>
                            <option value="vacation">Férias</option>
                            <option value="sick">Licença Médica</option>
                            <option value="personal">Folga Pessoal</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Início *</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Término *</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Solicitar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

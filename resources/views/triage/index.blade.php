@extends('layouts.adminlte', ['title' => 'Triagem'])
@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Pacientes Aguardando</h3></div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead><tr><th>Severidade</th><th>Pet</th><th>Chegada</th><th>Queixa</th><th>Status</th><th>Ações</th></tr></thead>
                        <tbody>
                        @foreach($waiting as $t)
                            <tr class="{{ $t->severity === 'red' ? 'table-danger' : ($t->severity === 'orange' ? 'table-warning' : ($t->severity === 'yellow' ? 'table-info' : '')) }}">
                                <td><span class="badge badge-{{ $t->severity === 'red' ? 'danger' : ($t->severity === 'orange' ? 'warning' : ($t->severity === 'yellow' ? 'info' : 'success')) }}">{{ strtoupper($t->severity) }}</span></td>
                                <td>{{ $t->pet->name ?? '-' }}</td>
                                <td>{{ optional($t->check_in_at)->format('H:i') }}</td>
                                <td>{{ Str::limit($t->chief_complaint, 50) }}</td>
                                <td>{{ $t->status }}</td>
                                <td><a href="{{ route('triage.show', $t) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><a href="{{ route('triage.create') }}" class="btn btn-primary btn-block">Novo Paciente</a></div>
                <div class="card-body">
                    <h5>Legenda</h5>
                    <p><span class="badge badge-danger">VERMELHO</span> Emergência</p>
                    <p><span class="badge badge-warning">LARANJA</span> Urgência</p>
                    <p><span class="badge badge-info">AMARELO</span> Prioritário</p>
                    <p><span class="badge badge-success">VERDE</span> Não urgente</p>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Histórico</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead><tr><th>Pet</th><th>Status</th></tr></thead>
                        <tbody>
                        @foreach($history as $h)
                            <tr><td>{{ $h->pet->name ?? '-' }}</td><td>{{ $h->status }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $history->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.adminlte', ['title' => 'Substância Controlada - ' . $controlledSubstance->name])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $controlledSubstance->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('controlled-substances.edit', $controlledSubstance) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('controlled-substance-logs.create', ['controlled_substance_id' => $controlledSubstance->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-exchange-alt"></i> Movimentar
                    </a>
                    <a href="{{ route('controlled-substances.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Princípio Ativo:</strong>
                        <p>{{ $controlledSubstance->active_ingredient ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Lista/Controle:</strong>
                        <p>{{ $controlledSubstance->schedule ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Registro ANVISA:</strong>
                        <p>{{ $controlledSubstance->anvisa_register ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Unidade:</strong>
                        <p>{{ $controlledSubstance->unit }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Estoque Atual</span>
                                <span class="info-box-number">{{ number_format($controlledSubstance->current_stock, 2, ',', '.') }} {{ $controlledSubstance->unit }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Estoque Mínimo</span>
                                <span class="info-box-number">{{ number_format($controlledSubstance->min_stock, 2, ',', '.') }} {{ $controlledSubstance->unit }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-{{ $controlledSubstance->is_active ? 'success' : 'secondary' }}">
                            <span class="info-box-icon"><i class="fas fa-{{ $controlledSubstance->is_active ? 'check-circle' : 'minus-circle' }}"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Status</span>
                                <span class="info-box-number">{{ $controlledSubstance->is_active ? 'Ativo' : 'Inativo' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($controlledSubstance->notes)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Observações:</strong>
                    <p class="mt-1">{!! $controlledSubstance->notes !!}</p>
                </div>
                @endif

                <hr>
                <h5>Movimentações de Estoque</h5>
                @if($controlledSubstance->logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Saldo Antes</th>
                                <th>Saldo Depois</th>
                                <th>Responsável</th>
                                <th>Testemunha</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($controlledSubstance->logs->sortByDesc('created_at') as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($log->type === 'in')
                                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Entrada</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Saída</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ number_format($log->quantity, 2, ',', '.') }} {{ $controlledSubstance->unit }}</td>
                                <td>{{ number_format($log->balance_before, 2, ',', '.') }}</td>
                                <td>{{ number_format($log->balance_after, 2, ',', '.') }}</td>
                                <td>{{ $log->user->name ?? '-' }}</td>
                                <td>{{ $log->witness->name ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $log->reason ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Nenhuma movimentação registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

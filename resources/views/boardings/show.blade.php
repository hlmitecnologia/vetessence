@extends('layouts.adminlte', ['title' => 'Hospedagem'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $boarding->pet->name ?? 'N/A' }} - {{ $boarding->type == 'boarding' ? 'Hospedagem' : ($boarding->type == 'grooming' ? 'Banho/Tosa' : 'Ambos') }}</h3>
        <div class="card-tools">
            @if($boarding->status == 'checked_in')
                <a href="{{ route('boardings.edit', $boarding) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            @endif
            <a href="{{ route('boardings.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Pet:</strong><p>{{ $boarding->pet->name ?? 'N/A' }}</p></div>
            <div class="col-md-3"><strong>Check-in:</strong><p>{{ $boarding->check_in_at->format('d/m/Y H:i') }}</p></div>
            <div class="col-md-3"><strong>Check-out Previsto:</strong><p>{{ optional($boarding->expected_check_out)->format('d/m/Y') ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Status:</strong><p>
                @if($boarding->status == 'checked_in') <span class="badge badge-success">Hospedado</span>
                @elseif($boarding->status == 'checked_out') <span class="badge badge-secondary">Finalizado</span>
                @else <span class="badge badge-danger">Cancelado</span> @endif
            </p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3"><strong>Diária:</strong><p>R$ {{ number_format($boarding->daily_rate, 2, ',', '.') }}</p></div>
            <div class="col-md-3"><strong>Taxa Banho/Tosa:</strong><p>R$ {{ number_format($boarding->grooming_fee, 2, ',', '.') }}</p></div>
            <div class="col-md-3"><strong>Total:</strong><p class="h5">R$ {{ number_format($boarding->total_amount, 2, ',', '.') }}</p></div>
            <div class="col-md-3"><strong>Dias:</strong><p>{{ $boarding->daysBoarded() }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Responsável Check-in:</strong><p>{{ $boarding->createdBy->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Responsável Check-out:</strong><p>{{ $boarding->checkedOutBy->name ?? 'Pendente' }}</p></div>
            <div class="col-md-4"><strong>Contato Retirada:</strong><p>{{ $boarding->pickup_contact ?? '-' }}</p></div>
        </div>
        @if($boarding->reason)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Motivo:</strong><p>{!! $boarding->reason !!}</p></div>
        </div>
        @endif
        @if($boarding->feeding_instructions)
        <div class="row mt-2">
            <div class="col-md-6"><strong>Alimentação:</strong><p>{!! $boarding->feeding_instructions !!}</p></div>
        </div>
        @endif
        @if($boarding->medication_instructions)
        <div class="row mt-2">
            <div class="col-md-6"><strong>Medicação:</strong><p>{!! $boarding->medication_instructions !!}</p></div>
        </div>
        @endif
        @if($boarding->notes)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Observações:</strong><p>{!! $boarding->notes !!}</p></div>
        </div>
        @endif
        @if($boarding->check_out_at)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Check-out realizado em:</strong><p>{{ $boarding->check_out_at->format('d/m/Y H:i') }}</p></div>
        </div>
        @endif
    </div>
</div>

@if($boarding->status == 'checked_in')
<div class="card">
    <div class="card-header"><h3 class="card-title">Ações</h3></div>
    <div class="card-body">
        <form action="{{ route('boardings.checkout', $boarding) }}" method="POST" class="d-inline">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <input type="datetime-local" name="check_out_at" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" name="total_amount" class="form-control form-control-sm" value="{{ $boarding->daily_rate * $boarding->daysBoarded() + $boarding->grooming_fee }}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Obs. do check-out">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm" data-confirm="Realizar check-out?">
                        <i class="fas fa-sign-out-alt"></i> Check-out
                    </button>
                </div>
                <div class="col-md-2">
                    <form action="{{ route('boardings.cancel', $boarding) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="Cancelar hospedagem?">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Daily Tasks -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tarefas Diárias</h3>
        <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#taskModal">
            <i class="fas fa-plus"></i> Nova Tarefa
        </button>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tarefa</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Concluído por</th>
                    <th>Obs</th>
                    <th style="width: 80px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boarding->dailyTasks->sortByDesc('task_date') as $task)
                <tr>
                    <td>{{ $task->task_date->format('d/m/Y') }}</td>
                    <td>{{ $task->task_name }}</td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $task->description ?? '-' }}</td>
                    <td>
                        @if($task->is_completed)
                            <span class="badge badge-success">OK</span>
                        @else
                            <span class="badge badge-warning">Pendente</span>
                        @endif
                    </td>
                    <td>{{ $task->completedBy->name ?? '-' }}</td>
                    <td class="text-truncate" style="max-width: 150px;">{{ $task->observations ?? '-' }}</td>
                    <td>
                        @if(!$task->is_completed)
                        <form action="{{ route('boardings.tasks.complete', [$boarding, $task]) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action btn-success" title="Concluir"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                        <form action="{{ route('boardings.tasks.destroy', [$boarding, $task]) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-action btn-danger" title="Excluir" data-confirm="Excluir tarefa?"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">Nenhuma tarefa cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('boardings.tasks.store', $boarding) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>Nova Tarefa</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="task_date">Data *</label>
                        <input type="date" name="task_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="task_name">Tarefa *</label>
                        <select name="task_name" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Passeio">Passeio</option>
                            <option value="Medicação">Medicação</option>
                            <option value="Banho">Banho</option>
                            <option value="Higiene">Higiene</option>
                            <option value="Brinquedo/Tempo Livre">Brinquedo/Tempo Livre</option>
                            <option value="Sinais Vitais">Sinais Vitais</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" rows="2" class="wysiwyg form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>$('#taskModal').on('shown.bs.modal', function() { $(this).find('[name=task_name]').focus(); });</script>
@endpush

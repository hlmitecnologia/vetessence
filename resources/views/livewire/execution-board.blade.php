<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button wire:click="previousDay" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i>
            </button>
            <strong class="mx-3">{{ now()->parse($date)->format('d/m/Y') }}</strong>
            <button wire:click="nextDay" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div>
            @can('execution-maps.manage')
                <button wire:click="generateFromPrescriptions" class="btn btn-sm btn-primary">
                    <i class="fas fa-prescription"></i> Gerar de Prescrições
                </button>
            @endcan
            <button wire:click="addManualTask" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Adicionar Procedimento
            </button>
        </div>
    </div>

    @if (empty($tasks))
        <div class="text-center text-muted py-4">
            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
            <p>Nenhuma tarefa para esta data. Gere a partir das prescrições ou adicione manualmente.</p>
        </div>
    @else
        @php $grouped = $this->groupedTasks; @endphp
        @foreach (['morning' => 'Manhã (06–12)', 'afternoon' => 'Tarde (12–18)', 'night' => 'Noite (18–06)'] as $period => $label)
            @if (count($grouped[$period]) > 0)
                <h6 class="text-muted border-bottom pb-1 mb-2">{{ $label }}</h6>
                <div class="list-group mb-3">
                    @foreach ($grouped[$period] as $task)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
                            {{ $task['scheduled_time'] && $task['scheduled_time'] < now()->format('H:i:s') && in_array($task['status'], ['pending', 'in_progress']) ? 'border-danger' : '' }}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <strong>{{ $task['title'] }}</strong>
                                    @if ($task['dosage'])
                                        <span class="badge badge-info">{{ $task['dosage'] }} {{ $task['unit'] }}</span>
                                    @endif
                                    @if ($task['route'])
                                        <span class="badge badge-secondary">{{ $task['route'] }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    @if ($task['scheduled_time'])
                                        {{ substr($task['scheduled_time'], 0, 5) }}h
                                    @else
                                        Horário livre
                                    @endif
                                    @if ($task['description'])
                                        &mdash; {{ $task['description'] }}
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $badgeClass = match($task['status']) {
                                        'completed' => 'badge-success',
                                        'in_progress' => 'badge-warning',
                                        'skipped' => 'badge-secondary',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-light',
                                    };
                                    $labelMap = [
                                        'pending' => 'Pendente',
                                        'in_progress' => 'Em Andamento',
                                        'completed' => 'Concluído',
                                        'skipped' => 'Pulado',
                                        'cancelled' => 'Cancelado',
                                    ];
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $labelMap[$task['status']] ?? $task['status'] }}</span>
                                @if (in_array($task['status'], ['pending', 'in_progress']))
                                    <button wire:click="execute({{ $task['id'] }})" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check"></i> Executar
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

    @if ($showManualTaskModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Procedimento</h5>
                        <button type="button" class="close" wire:click="$set('showManualTaskModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Título <span class="text-danger">*</span></label>
                            <input wire:model="manualTaskTitle" class="form-control">
                            @error('manualTaskTitle') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Categoria</label>
                            <select wire:model="manualTaskCategory" class="form-control">
                                <option value="procedure">Procedimento</option>
                                <option value="medication">Medicação</option>
                                <option value="exam">Exame</option>
                                <option value="care">Cuidado</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Horário</label>
                            <input wire:model="manualTaskTime" type="time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea wire:model="manualTaskDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showManualTaskModal', false)">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="saveManualTask">Adicionar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showExecuteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Execução</h5>
                        <button type="button" class="close" wire:click="$set('showExecuteModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select wire:model="executeStatus" class="form-control">
                                <option value="completed">Completo</option>
                                <option value="partially">Parcial</option>
                                <option value="skipped">Pulado</option>
                            </select>
                            @error('executeStatus') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea wire:model="executeNotes" class="form-control" rows="3"></textarea>
                            @error('executeNotes') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showExecuteModal', false)">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="confirmExecution">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

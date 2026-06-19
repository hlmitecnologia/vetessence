<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-clipboard-list"></i> Mapa de Execução
                </h3>
                <div class="d-flex gap-2">
                    <select wire:model.live="statusFilter" class="form-control form-control-sm" style="width: auto;">
                        <option value="">Todos os Status</option>
                        <option value="admitted">Internado</option>
                        <option value="discharged">Alta</option>
                        <option value="transferred">Transferido</option>
                    </select>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" placeholder="Buscar pet ou tutor..." style="width: 200px;">
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Tutor</th>
                        <th>Data Internação</th>
                        <th>Status</th>
                        <th>Pendentes Hoje</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hospitalizations as $hosp)
                        @php
                            $statusLabel = match($hosp->status) {
                                'admitted' => 'Internado',
                                'discharged' => 'Alta',
                                'transferred' => 'Transferido',
                                default => ucfirst($hosp->status),
                            };
                            $statusBadge = match($hosp->status) {
                                'admitted' => 'badge-success',
                                'discharged' => 'badge-secondary',
                                'transferred' => 'badge-warning',
                                default => 'badge-primary',
                            };
                            $pendingToday = $hosp->executionMaps()
                                ->where('date', now()->toDateString())
                                ->first()
                                ?->tasks()
                                ->where('status', 'pending')
                                ->count() ?? 0;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $hosp->pet->name ?? '—' }}</strong>
                            </td>
                            <td>{{ $hosp->tutor->name ?? '—' }}</td>
                            <td>{{ $hosp->admission_date ? $hosp->admission_date->format('d/m/Y') : '—' }}</td>
                            <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                            <td>
                                @if ($pendingToday > 0)
                                    <span class="badge badge-warning">{{ $pendingToday }} pendente(s)</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('hospitalizations.show', $hosp->id) }}#execucao" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Abrir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>Nenhuma internação encontrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($hospitalizations->hasPages())
            <div class="card-footer">
                {{ $hospitalizations->links() }}
            </div>
        @endif
    </div>
</div>

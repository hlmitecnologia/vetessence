<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pacientes Aguardando</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $waiting->count() }} aguardando</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Severidade</th>
                                <th>Pet</th>
                                <th>Chegada</th>
                                <th>Queixa</th>
                                <th>Veterinário</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($waiting as $t)
                            <tr class="{{ $t->severity === 'red' ? 'table-danger' : ($t->severity === 'orange' ? 'table-warning' : ($t->severity === 'yellow' ? 'table-info' : '')) }}">
                                <td>
                                    <span class="badge badge-{{ $t->severity === 'red' ? 'danger' : ($t->severity === 'orange' ? 'warning' : ($t->severity === 'yellow' ? 'info' : 'success')) }}">
                                        {{ strtoupper($t->severity) }}
                                    </span>
                                </td>
                                <td>{{ $t->pet->name ?? '-' }}</td>
                                <td>{{ optional($t->check_in_at)->format('H:i') }}</td>
                                <td>{{ Str::limit($t->chief_complaint, 50) }}</td>
                                <td>{{ $t->assignedVet->name ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('triage.show', $t) }}" class="btn btn-info" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($t->status === 'waiting')
                                        <button wire:click="markAsInConsultation({{ $t->id }})" class="btn btn-primary" title="Iniciar consulta">
                                            <i class="fas fa-user-md"></i>
                                        </button>
                                        @endif
                                        @if($t->status === 'in_consultation')
                                        <button wire:click="markAsSeen({{ $t->id }})" class="btn btn-success" title="Finalizar consulta">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('triage.edit', $t) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    Nenhum paciente aguardando.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('triage.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Novo Paciente
                    </a>
                </div>
                <div class="card-body">
                    <h5>Legenda</h5>
                    <p><span class="badge badge-danger">VERMELHO</span> Emergência</p>
                    <p><span class="badge badge-warning">LARANJA</span> Urgência</p>
                    <p><span class="badge badge-info">AMARELO</span> Prioritário</p>
                    <p><span class="badge badge-success">VERDE</span> Não urgente</p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Histórico</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Severidade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $h)
                            <tr>
                                <td>{{ $h->pet->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $h->severity === 'red' ? 'danger' : ($h->severity === 'orange' ? 'warning' : ($h->severity === 'yellow' ? 'info' : 'success')) }}">
                                        {{ strtoupper($h->severity) }}
                                    </span>
                                </td>
                                <td>{{ $h->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $history->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('new-red-triage', function (e) {
            var ids = e.detail.ids;
            if (ids && ids.length > 0) {
                var sound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZm');
                if (sound) sound.play().catch(function(){});
            }
        });
    </script>
</div>

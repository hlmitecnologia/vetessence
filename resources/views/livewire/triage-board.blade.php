<div wire:poll.5s>
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="mb-0">Painel de Triagem em Tempo Real</h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('triage.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Paciente
            </a>
        </div>
    </div>

    <div x-data="{
        newRedAlert: {{ ($newRed ?? false) ? 'true' : 'false' }},
        dismissed: false,
        init() {
            if (this.newRedAlert) {
                this.playSound();
                setTimeout(() => { this.dismissed = true; }, 8000);
            }
        },
        playSound() {
            try {
                new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZm').play();
            } catch(e) {}
        }
    }">
        <div x-show="newRedAlert && !dismissed" x-transition.duration.500ms
             class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Alerta!</strong> Novo(s) paciente(s) crítico(s) (Vermelho) chegou(aram) na triagem!
            <button type="button" class="close" @click="dismissed = true">&times;</button>
        </div>
    </div>

    <div class="row" style="min-height: 70vh;">
        @php
            $columns = [
                'waiting' => ['title' => 'Aguardando', 'color' => 'secondary'],
                'in_consultation' => ['title' => 'Em Atendimento', 'color' => 'primary'],
                'seen' => ['title' => 'Concluído', 'color' => 'success'],
            ];
            $severityLabels = ['red' => 'Crítico', 'orange' => 'Urgente', 'yellow' => 'Prioritário', 'green' => 'Não Urgente'];
            $severityBadge = ['red' => 'danger', 'orange' => 'warning', 'yellow' => 'info', 'green' => 'success'];
            $severityOrder = ['red' => 0, 'orange' => 1, 'yellow' => 2, 'green' => 3];
        @endphp

        @foreach($columns as $statusKey => $col)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-{{ $col['color'] }} text-white">
                        <strong>{{ $col['title'] }}</strong>
                        <span class="badge badge-light float-right">
                            {{ isset($triageCases[$statusKey]) ? $triageCases[$statusKey]->sum(function($c) { return 1; }) : 0 }}
                        </span>
                    </div>
                    <div class="card-body p-2"
                         x-on:dragover.prevent
                         x-on:drop.prevent="
                             const id = $event.dataTransfer.getData('text/plain');
                             if (id) $wire.updateStatus(id, '{{ $statusKey }}');
                         "
                         style="background: #f8f9fa; min-height: 300px;">
                        @php
                            $cases = isset($triageCases[$statusKey]) ? $triageCases[$statusKey]->groupBy('severity') : collect();
                            $sorted = collect($severityOrder)->mapWithKeys(function($order, $sev) use ($cases) {
                                return [$sev => $cases->get($sev, collect())];
                            })->filter(function($c) { return $c->count() > 0; });
                        @endphp

                        @forelse($sorted as $severity => $group)
                            <div class="mb-2">
                                <small class="text-muted font-weight-bold">
                                    <span class="badge badge-{{ $severityBadge[$severity] ?? 'secondary' }}">
                                        {{ $severityLabels[$severity] ?? strtoupper($severity) }}
                                    </span>
                                    ({{ $group->count() }})
                                </small>
                                @foreach($group as $t)
                                    <div class="card card-outline card-{{ $severityBadge[$severity] ?? 'secondary' }} mb-1"
                                         draggable="true"
                                         x-on:dragstart="
                                             $event.dataTransfer.setData('text/plain', {{ $t->id }});
                                             $event.dataTransfer.effectAllowed = 'move';
                                         "
                                         x-on:dragend="
                                             $event.target.closest('.card-outline').classList.remove('dragging');
                                         "
                                         style="cursor: grab;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <strong class="text-sm">{{ $t->pet->name ?? '—' }}</strong>
                                                <small class="text-muted">
                                                    {{ optional($t->check_in_at)->diffForHumans() }}
                                                </small>
                                            </div>
                                            <small class="d-block text-muted">
                                                Tutor: {{ $t->pet->tutors->first()->name ?? '—' }}
                                            </small>
                                            <small class="d-block text-muted">
                                                {{ Str::limit($t->chief_complaint, 60) }}
                                            </small>
                                            @if($t->assignedVet)
                                                <small class="d-block text-muted">
                                                    Vet: {{ $t->assignedVet->name }}
                                                </small>
                                            @endif
                                            <div class="mt-1">
                                                <a href="{{ route('triage.show', $t) }}" class="btn btn-xs btn-info" title="Ver detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('triage.edit', $t) }}" class="btn btn-xs btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <small>Nenhum caso</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Legenda</h3>
                </div>
                <div class="card-body py-2">
                    <span class="badge badge-danger">Vermelho</span> Crítico &nbsp;
                    <span class="badge badge-warning">Laranja</span> Urgente &nbsp;
                    <span class="badge badge-info">Amarelo</span> Prioritário &nbsp;
                    <span class="badge badge-success">Verde</span> Não urgente &nbsp;
                    <small class="text-muted ml-3">Arraste os cartões entre as colunas para alterar o status.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('new-red-triage', function (e) {
            var ids = e.detail.ids;
            if (ids && ids.length > 0) {
                try {
                    new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZm').play();
                } catch(e) {}
            }
        });
    </script>
</div>
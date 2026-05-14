@extends('layouts.adminlte', ['title' => 'Mapa de Canis'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Mapa de Canis</h3>
        <div class="card-tools">
            <a href="{{ route('boardings.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Check-in</a>
        </div>
    </div>
    <div class="card-body">
        @if($kennels->isEmpty())
            <p class="text-muted">Nenhum canil cadastrado.</p>
        @else
            <div class="row">
                @foreach($kennels as $kennel)
                    @php
                        $active = $kennel->activeBoardings;
                        $isFull = $active->count() >= $kennel->capacity;
                        $statusColor = $isFull ? 'danger' : ($active->count() > 0 ? 'warning' : 'success');
                    @endphp
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card border-{{ $statusColor }}">
                            <div class="card-header bg-{{ $statusColor }} text-white">
                                <h5 class="card-title mb-0">{{ $kennel->name }}</h5>
                                <span class="float-right">{{ $active->count() }}/{{ $kennel->capacity }}</span>
                            </div>
                            <div class="card-body p-2">
                                @if($active->isEmpty())
                                    <p class="text-muted mb-0 text-center small">Disponível</p>
                                @else
                                    @foreach($active as $boarding)
                                        <div class="d-flex justify-content-between align-items-center mb-1 p-1 bg-light rounded">
                                            <span>{{ $boarding->pet->name ?? 'N/A' }}</span>
                                            <small>{{ $boarding->check_in_at->format('d/m') }}</small>
                                        </div>
                                    @endforeach
                                @endif
                                @if($kennel->notes)
                                    <small class="text-muted d-block mt-1">{{ $kennel->notes }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Hospedagens Ativas</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Check-in</th><th>Tipo</th><th>Canil</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeBoardings as $b)
                <tr>
                    <td>{{ $b->pet->name ?? 'N/A' }}</td>
                    <td>{{ $b->check_in_at->format('d/m/Y') }}</td>
                    <td>{{ $b->type }}</td>
                    <td>{{ $b->kennel->name ?? '-' }}</td>
                    <td><a href="{{ route('boardings.show', $b) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Nenhuma hospedagem ativa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

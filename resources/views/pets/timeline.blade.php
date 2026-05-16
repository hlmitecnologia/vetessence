@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-history"></i> Timeline — {{ $pet->name }}</h4>
        <a href="{{ route('pets.show', $pet) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar ao Pet</a>
    </div>

    <div class="card mb-3">
        <div class="card-body py-2">
            <strong>{{ $pet->name }}</strong> —
            {{ $pet->species ?? '' }} {{ $pet->breed ?? '' }} {{ $pet->gender ? '(' . $pet->gender . ')' : '' }}
            @if($pet->age) | {{ $pet->age }} @endif
            @if($pet->weight) | {{ $pet->weight }} kg @endif
        </div>
    </div>

    <div class="timeline">
        @forelse($events as $event)
            <div class="timeline-item mb-3">
                <div class="row">
                    <div class="col-auto text-center" style="width: 80px;">
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($event['date'])->format('d/m') }}</div>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($event['date'])->format('H:i') }}</div>
                    </div>
                    <div class="col-auto">
                        <span class="badge badge-{{ $event['color'] }}" style="font-size: 1rem;">
                            <i class="fas {{ $event['icon'] }}"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="card card-{{ $event['color'] }} card-outline">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $event['type'] }}</strong>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0">{{ $event['summary'] }}</p>
                                @if($event['url'])
                                    <a href="{{ $event['url'] }}" class="btn btn-xs btn-outline-{{ $event['color'] }} mt-1">Ver detalhes</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center">Nenhum evento registrado para este paciente.</p>
        @endforelse
    </div>
</div>
@endsection

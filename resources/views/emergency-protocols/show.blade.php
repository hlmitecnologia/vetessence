@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-ambulance"></i> {{ $emergencyProtocol->title }}</h4>
        <div>
            <a href="{{ route('emergency-protocols.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
            @can('emergency-protocols.edit')
            <a href="{{ route('emergency-protocols.edit', $emergencyProtocol) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
            @endcan
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <span class="badge badge-{{ $emergencyProtocol->severity == 'critical' ? 'danger' : ($emergencyProtocol->severity == 'urgent' ? 'warning' : 'success') }} badge-lg">
                {{ ucfirst($emergencyProtocol->severity) }}
            </span>
            @if($emergencyProtocol->species) <span class="ml-2 badge badge-info">{{ $emergencyProtocol->species }}</span> @endif
            @if($emergencyProtocol->category) <span class="ml-2 badge badge-secondary">{{ $emergencyProtocol->category }}</span> @endif
            @if(!$emergencyProtocol->is_active) <span class="ml-2 badge badge-dark">Inativo</span> @endif
        </div>
        <div class="card-body">
            @if($emergencyProtocol->description)
            <h6>Descricao</h6>
            <p>{!! $emergencyProtocol->description !!}</p>
            <hr>
            @endif
            <h6>Procedimento</h6>
            <div class="p-3 bg-light rounded">{!! nl2br(e($emergencyProtocol->procedure_steps)) !!}</div>
            @if($emergencyProtocol->medications)
            <hr>
            <h6>Medicamentos</h6>
            <p>{{ $emergencyProtocol->medications }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-ambulance"></i> Protocolos de Emergencia</h4>
        @can('emergency-protocols.create')
        <a href="{{ route('emergency-protocols.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo</a>
        @endcan
    </div>
    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <select name="species" class="form-control" onchange="this.form.submit()">
                <option value="">Todas as especies</option>
                @foreach($speciesList as $s)
                <option value="{{ $s }}" {{ request('species') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="severity" class="form-control" onchange="this.form.submit()">
                <option value="">Todas as gravidades</option>
                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critico</option>
                <option value="urgent" {{ request('severity') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                <option value="stable" {{ request('severity') == 'stable' ? 'selected' : '' }}>Estavel</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-secondary w-100"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
    </form>
    <div class="row">
        @forelse($protocols as $p)
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $p->title }}</strong>
                    <span class="badge badge-{{ $p->severity == 'critical' ? 'danger' : ($p->severity == 'urgent' ? 'warning' : 'success') }}">
                        {{ ucfirst($p->severity) }}
                    </span>
                </div>
                <div class="card-body">
                    @if($p->species)<small class="text-muted"><i class="fas fa-paw"></i> {{ $p->species }}</small> @endif
                    @if($p->category)<small class="text-muted ml-2"><i class="fas fa-tag"></i> {{ $p->category }}</small>@endif
                    <p class="mt-2">{{ Str::limit($p->description, 100) }}</p>
                    @if($p->medications)
                    <small><i class="fas fa-pills"></i> {{ Str::limit($p->medications, 80) }}</small>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('emergency-protocols.show', $p) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver</a>
                    @can('emergency-protocols.edit')
                    <a href="{{ route('emergency-protocols.edit', $p) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    @endcan
                    @can('emergency-protocols.delete')
                    <form action="{{ route('emergency-protocols.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p class="text-center text-muted">Nenhum protocolo encontrado.</p></div>
        @endforelse
    </div>
    {{ $protocols->links() }}
</div>
@endsection

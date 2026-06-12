@extends('layouts.adminlte', ['title' => $service->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Preço</small>
                        <p class="h3 font-weight-bold">R$ {{ number_format($service->price, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Duração</small>
                        <p>{{ $service->duration ? $service->duration . ' minutos' : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Categoria</small>
                        <p>{{ $service->category->name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Status</small>
                        <span class="badge {{ $service->is_active ? 'badge-success' : 'badge-danger' }}">{{ $service->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </div>
                </div>
                @if($service->description)
                <hr>
                <small class="text-muted text-uppercase">Descrição</small>
                <p>{!! $service->description !!}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('services.edit', $service) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection

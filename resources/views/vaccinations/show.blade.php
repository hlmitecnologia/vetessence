@extends('layouts.adminlte', ['title' => 'Vacina - ' . $vaccination->pet->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Pet</small>
                        <p class="font-weight-bold">{{ $vaccination->pet->name }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Vacina</small>
                        <p class="font-weight-bold">{{ $vaccination->vaccine }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Data</small>
                        <p>{{ $vaccination->date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Próxima Dose</small>
                        <p>{{ $vaccination->next_date ? $vaccination->next_date->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Lote</small>
                        <p>{{ $vaccination->batch ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Fabricante</small>
                        <p>{{ $vaccination->manufacturer ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Veterinário</small>
                        <p>{{ $vaccination->vet->name ?? '-' }}</p>
                    </div>
                </div>
                @if($vaccination->notes)
                <hr>
                <p>{{ $vaccination->notes }}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('vaccinations.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('vaccinations.edit', $vaccination) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection

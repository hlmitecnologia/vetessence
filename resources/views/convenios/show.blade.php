@extends('layouts.adminlte', ['title' => $convenio->name])

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">CNPJ</small>
                        <p>{{ $convenio->cnpj ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Plano</small>
                        <p>{{ $convenio->plan_name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Desconto</small>
                        <p>{{ $convenio->discount_percent ? $convenio->discount_percent . '%' : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Limite/Mês</small>
                        <p>{{ $convenio->max_consults_month ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Início</small>
                        <p>{{ $convenio->start_date ? $convenio->start_date->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Fim</small>
                        <p>{{ $convenio->end_date ? $convenio->end_date->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
                @if($convenio->coverage)
                <hr>
                <small class="text-muted text-uppercase">Coberturas</small>
                <p>{!! $convenio->coverage !!}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('convenios.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('convenios.edit', $convenio) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
    <div class="col-md-6">
        @livewire('convenio-coverage-rules', ['convenio' => $convenio], key('coverage-rules-' . $convenio->id))
    </div>
</div>
@endsection

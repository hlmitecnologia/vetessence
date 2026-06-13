@extends('layouts.adminlte', ['title' => 'Controle Parasitário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $parasiteControl->product_name }} - {{ $parasiteControl->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('parasite-controls.edit', $parasiteControl) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('parasite-controls.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Pet:</strong><p>{{ $parasiteControl->pet->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Produto:</strong><p class="h4">{{ $parasiteControl->product_name }}</p></div>
            <div class="col-md-4"><strong>Princípio Ativo:</strong><p>{{ $parasiteControl->active_ingredient ?? '-' }}</p></div>
        </div>
        <div class="row">
            <div class="col-md-3"><strong>Tipo:</strong><p>@php $typeLabels = ['flea' => 'Pulga', 'tick' => 'Carrapato', 'heartworm' => 'Vermes', 'intestinal' => 'Intestinal', 'combination' => 'Combinado']; @endphp{{ $typeLabels[$parasiteControl->type] ?? $parasiteControl->type }}</p></div>
            <div class="col-md-3"><strong>Data Aplicação:</strong><p>{{ $parasiteControl->application_date->format('d/m/Y') }}</p></div>
            <div class="col-md-3"><strong>Próxima Data:</strong><p>{{ $parasiteControl->next_due_date ? $parasiteControl->next_due_date->format('d/m/Y') : '-' }}</p></div>
            <div class="col-md-3"><strong>Dosagem:</strong><p>{{ $parasiteControl->dose ?? '-' }}</p></div>
        </div>
        <div class="row">
            <div class="col-md-4"><strong>Lote:</strong><p>{{ $parasiteControl->batch ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Veterinário:</strong><p>{{ $parasiteControl->vet->name ?? '-' }}</p></div>
        </div>
        @if($parasiteControl->notes)
        <div class="row mt-3"><div class="col-md-12"><strong>Observações:</strong><p>{!! $parasiteControl->notes !!}</p></div></div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => 'Exame - ' . ($exam->pet->name ?? '-')])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Pet</small>
                        <p class="font-weight-bold">{{ $exam->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Tipo</small>
                        <p class="font-weight-bold">{{ $exam->type }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Data Solicitação</small>
                        <p>{{ $exam->requested_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Status</small>
                        @php $statusLabels = ['requested' => 'Solicitado', 'collected' => 'Coletado', 'analyzing' => 'Analisando', 'ready' => 'Pronto', 'delivered' => 'Entregue']; @endphp
                        <span class="badge badge-info">{{ $statusLabels[$exam->status] ?? $exam->status }}</span>
                    </div>
                </div>
                @if($exam->result)
                <hr>
                <small class="text-muted text-uppercase">Resultado</small>
                <p class="bg-light p-3 rounded">{{ $exam->result }}</p>
                @endif
                @if($exam->notes)
                <small class="text-muted text-uppercase">Observações</small>
                <p>{{ $exam->notes }}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection

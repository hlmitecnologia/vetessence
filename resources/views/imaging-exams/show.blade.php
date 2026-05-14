@extends('layouts.adminlte', ['title' => 'Exame de Imagem - ' . $exam->exam_number])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Exame de Imagem - {{ $exam->exam_number }}</h3>
                <div class="card-tools">
                    <a href="{{ route('imaging-exams.edit', $exam) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('imaging-exams.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Pet:</strong>
                        <p>{{ $exam->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Veterinário:</strong>
                        <p>{{ $exam->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Tipo:</strong>
                        <p>
                            @php
                                $typeLabels = ['xray' => 'Raio-X', 'ultrasound' => 'Ultrassom', 'ct' => 'Tomografia', 'mri' => 'Ressonância', 'ecg' => 'Eletrocardiograma', 'endoscopy' => 'Endoscopia', 'other' => 'Outro'];
                            @endphp
                            {{ $typeLabels[$exam->exam_type] ?? $exam->exam_type }}
                        </p>
                    </div>
                    <div class="col-md-3">
                        <strong>Região:</strong>
                        <p>{{ $exam->region ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong>Data do Exame:</strong> <p>{{ $exam->exam_date->format('d/m/Y') }}</p></div>
                    <div class="col-md-4"><strong>Radiologista:</strong> <p>{{ $exam->radiologist->name ?? '-' }}</p></div>
                    <div class="col-md-4">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusLabels = ['scheduled' => 'Agendado', 'performed' => 'Realizado', 'reported' => 'Laudado', 'cancelled' => 'Cancelado'];
                                $statusColors = ['scheduled' => 'primary', 'performed' => 'warning', 'reported' => 'success', 'cancelled' => 'danger'];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$exam->status] ?? 'secondary' }}">
                                {{ $statusLabels[$exam->status] ?? $exam->status }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($exam->findings)
                <div class="mt-4">
                    <h5>Achados</h5>
                    <div class="p-3 bg-light rounded">
                        <p class="mb-0">{{ $exam->findings }}</p>
                    </div>
                </div>
                @endif

                @if($exam->impression)
                <div class="mt-4">
                    <h5>Impressão</h5>
                    <div class="p-3 bg-info-light rounded">
                        <p class="mb-0">{{ $exam->impression }}</p>
                    </div>
                </div>
                @endif

                @if($exam->recommendations)
                <div class="mt-4">
                    <h5>Recomendações</h5>
                    <div class="p-3 bg-warning-light rounded">
                        <p class="mb-0">{{ $exam->recommendations }}</p>
                    </div>
                </div>
                @endif

                @if($exam->images && count($exam->images) > 0)
                <div class="mt-4">
                    <h5>Imagens</h5>
                    <div class="row">
                        @foreach($exam->images as $image)
                        <div class="col-md-3 mb-3">
                            <a href="{{ $image }}" target="_blank">
                                <img src="{{ $image }}" alt="Imagem do exame" class="img-fluid img-thumbnail">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

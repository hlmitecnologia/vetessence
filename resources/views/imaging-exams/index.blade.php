@extends('layouts.adminlte', ['title' => 'Exames de Imagem'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Exames de Imagem</h3>
        <div class="card-tools">
            <a href="{{ route('imaging-exams.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Exame
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($exams->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº do Exame</th>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Região</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td><strong>{{ $exam->exam_number }}</strong></td>
                    <td>{{ $exam->pet->name ?? '-' }}</td>
                    <td>
                        @php
                            $typeLabels = ['xray' => 'Raio-X', 'ultrasound' => 'Ultrassom', 'ct' => 'Tomografia', 'mri' => 'Ressonância', 'ecg' => 'Eletrocardiograma', 'endoscopy' => 'Endoscopia', 'other' => 'Outro'];
                        @endphp
                        {{ $typeLabels[$exam->exam_type] ?? $exam->exam_type }}
                    </td>
                    <td>{{ $exam->region ?? '-' }}</td>
                    <td data-order="{{ $exam->exam_date->format('Y-m-d') }}">{{ $exam->exam_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $statusLabels = ['scheduled' => 'Agendado', 'performed' => 'Realizado', 'reported' => 'Laudado', 'cancelled' => 'Cancelado'];
                            $statusColors = ['scheduled' => 'primary', 'performed' => 'warning', 'reported' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$exam->status] ?? 'secondary' }}">
                            {{ $statusLabels[$exam->status] ?? $exam->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('imaging-exams.show', $exam) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('imaging-exams.edit', $exam) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum exame de imagem encontrado.</p>
        @endif
    </div>
</div>
@endsection

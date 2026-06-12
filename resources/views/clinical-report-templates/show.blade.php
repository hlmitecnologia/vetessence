@extends('layouts.adminlte', ['title' => 'Modelo de Laudo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $clinicalReportTemplate->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('clinical-report-templates.edit', $clinicalReportTemplate) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('clinical-report-templates.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Nome:</strong>
                <p>{{ $clinicalReportTemplate->name }}</p>
            </div>
            <div class="col-md-3">
                <strong>Espécie:</strong>
                <p>{{ $clinicalReportTemplate->species ?? 'Todas' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Especialidade:</strong>
                <p>{{ $clinicalReportTemplate->specialty ?? '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Categoria:</strong>
                <p>{{ $clinicalReportTemplate->category ?? '-' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Slug:</strong>
                <p>{{ $clinicalReportTemplate->slug }}</p>
            </div>
            <div class="col-md-6">
                <strong>Ativo:</strong>
                <p>
                    @if($clinicalReportTemplate->is_active)
                        <span class="badge badge-success">Sim</span>
                    @else
                        <span class="badge badge-secondary">Não</span>
                    @endif
                </p>
            </div>
        </div>
        @if($clinicalReportTemplate->description)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Descrição:</strong>
                <p>{!! $clinicalReportTemplate->description !!}</p>
            </div>
        </div>
        @endif
        @if($clinicalReportTemplate->content)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Conteúdo do Modelo:</strong>
                <div class="p-3 bg-light rounded border mt-2" style="white-space: pre-wrap; font-family: monospace;">
                    {!! $clinicalReportTemplate->content !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

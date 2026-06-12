@extends('layouts.adminlte', ['title' => 'Prescrição'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Prescrição</h3>
        <div class="card-tools">
            <a href="{{ route('prescriptions.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Pet:</dt>
                    <dd class="col-sm-8">
                        @if($prescription->medicalRecord && $prescription->medicalRecord->pet)
                            {{ $prescription->medicalRecord->pet->name }}
                        @else
                            N/A
                        @endif
                    </dd>
                    
                    <dt class="col-sm-4">Medicamento:</dt>
                    <dd class="col-sm-8">{{ $prescription->medication }}</dd>
                    
                    <dt class="col-sm-4">Dosagem:</dt>
                    <dd class="col-sm-8">{{ $prescription->dosage }}</dd>
                    
                    <dt class="col-sm-4">Frequência:</dt>
                    <dd class="col-sm-8">{{ $prescription->frequency }}</dd>
                    
                    <dt class="col-sm-4">Duração:</dt>
                    <dd class="col-sm-8">{{ $prescription->duration }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Data:</dt>
                    <dd class="col-sm-8">{{ $prescription->created_at->format('d/m/Y H:i') }}</dd>
                    
                    @if($prescription->instructions)
                    <dt class="col-sm-4">Instruções:</dt>
                    <dd class="col-sm-8">{!! $prescription->instructions !!}</dd>
                    @endif
                    
                    @if($prescription->notes)
                    <dt class="col-sm-4">Observações:</dt>
                    <dd class="col-sm-8">{!! $prescription->notes !!}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($prescription->isSigned())
        <div class="card card-success card-outline">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-shield-alt text-success mr-1"></i>Assinatura Digital</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">Este documento foi assinado digitalmente.</p>
                <p class="mb-1"><strong>Assinado em:</strong> {{ \Carbon\Carbon::parse($prescription->signed_at)->format('d/m/Y H:i:s') }}</p>
                <p class="mb-2">
                    <strong>Hash:</strong>
                    <code class="text-dark">{{ $prescription->content_hash }}</code>
                </p>
                <a href="{{ route('signature.verify', ['model' => 'prescription', 'id' => $prescription->id]) }}" target="_blank" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-check-circle mr-1"></i>Verificar Assinatura
                </a>
            </div>
        </div>
        @endif
    </div>
    </div>
    <div class="card-footer">
        <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Tem certeza que deseja excluir?">
                <i class="fas fa-trash"></i> Excluir
            </button>
        </form>
    </div>
</div>
@endsection
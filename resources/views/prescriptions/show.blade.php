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
                    <dd class="col-sm-8">{{ $prescription->instructions }}</dd>
                    @endif
                    
                    @if($prescription->notes)
                    <dt class="col-sm-4">Observações:</dt>
                    <dd class="col-sm-8">{{ $prescription->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">
                <i class="fas fa-trash"></i> Excluir
            </button>
        </form>
    </div>
</div>
@endsection
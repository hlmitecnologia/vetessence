@extends('layouts.adminlte', ['title' => 'Prescrições'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Prescrições</h3>
        <div class="card-tools">
            <a href="{{ route('prescriptions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($prescriptions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Medicamento</th>
                    <th>Dosagem</th>
                    <th>Frequência</th>
                    <th>Data</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $prescription)
                <tr>
                    <td>
                        @if($prescription->medicalRecord && $prescription->medicalRecord->pet)
                            {{ $prescription->medicalRecord->pet->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $prescription->medication }}</td>
                    <td>{{ $prescription->dosage }}</td>
                    <td>{{ $prescription->frequency }}</td>
                    <td>{{ $prescription->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection
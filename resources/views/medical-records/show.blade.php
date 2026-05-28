@extends('layouts.adminlte', ['title' => 'Prontuário - ' . ($medicalRecord->pet->name ?? '')])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-3">
                        <small class="text-muted text-uppercase">Pet</small>
                        <p class="font-weight-bold">{{ $medicalRecord->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-3">
                        <small class="text-muted text-uppercase">Veterinário</small>
                        <p class="font-weight-bold">{{ $medicalRecord->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-3">
                        <small class="text-muted text-uppercase">Data</small>
                        <p class="font-weight-bold">{{ $medicalRecord->date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-3">
                        <small class="text-muted text-uppercase">Tipo</small>
                        <span class="badge badge-info">{{ ucfirst($medicalRecord->type) }}</span>
                    </div>
                </div>

                @if($medicalRecord->chief_complaint)
                <div class="mb-3">
                    <small class="text-muted text-uppercase">Queixa Principal</small>
                    <p>{{ $medicalRecord->chief_complaint }}</p>
                </div>
                @endif

                @if($medicalRecord->anamnesis)
                <div class="mb-3">
                    <small class="text-muted text-uppercase">Anamnese</small>
                    <p class="whitespace-pre-line">{{ $medicalRecord->anamnesis }}</p>
                </div>
                @endif

                @if($medicalRecord->physical_exam)
                <div class="mb-3">
                    <small class="text-muted text-uppercase">Exame Físico</small>
                    <p class="whitespace-pre-line">{{ $medicalRecord->physical_exam }}</p>
                </div>
                @endif

                <div class="row">
                    @if($medicalRecord->diagnosis)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted text-uppercase">Diagnóstico</small>
                        <p>{{ $medicalRecord->diagnosis }}</p>
                    </div>
                    @endif
                    @if($medicalRecord->prognosis)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted text-uppercase">Prognóstico</small>
                        <p class="text-capitalize">{{ $medicalRecord->prognosis }}</p>
                    </div>
                    @endif
                </div>

                @if($medicalRecord->treatment)
                <div class="mb-3">
                    <small class="text-muted text-uppercase">Tratamento</small>
                    <p class="whitespace-pre-line">{{ $medicalRecord->treatment }}</p>
                </div>
                @endif

                @if($medicalRecord->notes)
                <small class="text-muted text-uppercase">Observações</small>
                <p class="bg-light p-3 rounded">{{ $medicalRecord->notes }}</p>
                @endif
            </div>
        </div>

        @if($medicalRecord->zoonoticDiseases->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-biohazard text-danger mr-2"></i>Doenças Zoonóticas</h5>
                @foreach($medicalRecord->zoonoticDiseases as $disease)
                <a href="{{ route('zoonotic-diseases.show', $disease) }}"
                   class="badge {{ $disease->pivot->is_suspected ? 'badge-warning' : 'badge-danger' }} mr-1 mb-1 p-2">
                    <i class="fas fa-biohazard mr-1"></i>{{ $disease->name }}
                    @if($disease->pivot->is_suspected) (Suspeito) @endif
                    @if($disease->is_notifiable) 🔔 @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($medicalRecord->prescriptions->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Prescrições</h5>
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Dosagem</th>
                            <th>Frequência</th>
                            <th>Duração</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicalRecord->prescriptions as $rx)
                        <tr>
                            <td>{{ $rx->medication }}</td>
                            <td>{{ $rx->dosage }} {{ $rx->unit }}</td>
                            <td>{{ $rx->frequency }}</td>
                            <td>{{ $rx->duration }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-between mt-3">
            <div>
                <a href="{{ route('medical-records.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i> Editar</a>
            </div>
            <form action="{{ route('medical-records.generate-invoice', $medicalRecord) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"><i class="fas fa-file-invoice mr-1"></i> Gerar Fatura</button>
            </form>
        </div>
    </div>
</div>
@endsection

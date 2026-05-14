@extends('layouts.adminlte', ['title' => 'Prontuário - ' . ($medicalRecord->pet->name ?? '')])

@section('header')
    <a href="{{ route('medical-records.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Prontuário - {{ $medicalRecord->pet->name ?? '-' }}</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Pet</h4>
                <p class="font-semibold">{{ $medicalRecord->pet->name ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Veterinário</h4>
                <p class="font-semibold">{{ $medicalRecord->vet->name ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Data</h4>
                <p class="font-semibold">{{ $medicalRecord->date->format('d/m/Y') }}</p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Tipo</h4>
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($medicalRecord->type) }}</span>
            </div>
        </div>

        @if($medicalRecord->chief_complaint)
        <div class="mb-4">
            <h4 class="text-xs text-gray-500 uppercase mb-1">Queixa Principal</h4>
            <p>{{ $medicalRecord->chief_complaint }}</p>
        </div>
        @endif

        @if($medicalRecord->anamnesis)
        <div class="mb-4">
            <h4 class="text-xs text-gray-500 uppercase mb-1">Anamnese</h4>
            <p class="whitespace-pre-line">{{ $medicalRecord->anamnesis }}</p>
        </div>
        @endif

        @if($medicalRecord->physical_exam)
        <div class="mb-4">
            <h4 class="text-xs text-gray-500 uppercase mb-1">Exame Físico</h4>
            <p class="whitespace-pre-line">{{ $medicalRecord->physical_exam }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($medicalRecord->diagnosis)
            <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-xs text-gray-500 uppercase mb-1">Diagnóstico</h4>
                <p>{{ $medicalRecord->diagnosis }}</p>
            </div>
            @endif
            @if($medicalRecord->prognosis)
            <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-xs text-gray-500 uppercase mb-1">Prognóstico</h4>
                <p class="capitalize">{{ $medicalRecord->prognosis }}</p>
            </div>
            @endif
        </div>

        @if($medicalRecord->treatment)
        <div class="mt-4">
            <h4 class="text-xs text-gray-500 uppercase mb-1">Tratamento</h4>
            <p class="whitespace-pre-line">{{ $medicalRecord->treatment }}</p>
        </div>
        @endif

        @if($medicalRecord->notes)
        <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
            <h4 class="text-xs text-gray-500 uppercase mb-1">Observações</h4>
            <p>{{ $medicalRecord->notes }}</p>
        </div>
        @endif
    </div>

    @if($medicalRecord->zoonoticDiseases->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-biohazard text-red-500 mr-2"></i>Doenças Zoonóticas</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($medicalRecord->zoonoticDiseases as $disease)
            <a href="{{ route('zoonotic-diseases.show', $disease) }}"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                      {{ $disease->pivot->is_suspected ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">
                <i class="fas fa-biohazard mr-2"></i>
                {{ $disease->name }}
                @if($disease->pivot->is_suspected)
                    <span class="ml-2 text-xs">(Suspeito)</span>
                @endif
                @if($disease->is_notifiable)
                    <span class="ml-2 text-xs">🔔</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($medicalRecord->prescriptions->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Prescrições</h3>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs">Medicamento</th>
                    <th class="px-4 py-2 text-left text-xs">Dosagem</th>
                    <th class="px-4 py-2 text-left text-xs">Frequência</th>
                    <th class="px-4 py-2 text-left text-xs">Duração</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($medicalRecord->prescriptions as $rx)
                <tr>
                    <td class="px-4 py-2">{{ $rx->medication }}</td>
                    <td class="px-4 py-2">{{ $rx->dosage }} {{ $rx->unit }}</td>
                    <td class="px-4 py-2">{{ $rx->frequency }}</td>
                    <td class="px-4 py-2">{{ $rx->duration }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="flex justify-between">
        <a href="{{ route('medical-records.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit mr-2"></i> Editar
        </a>
    </div>
</div>
@endsection

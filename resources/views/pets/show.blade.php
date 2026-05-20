@extends('layouts.adminlte', ['title' => $pet->name])

@section('header')
    <a href="{{ route('pets.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">{{ $pet->name }}</h2>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Dados do Pet -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                @if($pet->photo_url)
                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-32 h-32 rounded-full object-cover mx-auto">
                @else
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-4xl mx-auto">
                    <i class="fas fa-paw"></i>
                </div>
                @endif
                <h3 class="text-xl font-semibold mt-4">{{ $pet->name }}</h3>
                <p class="text-gray-500">
                    @php
                        $speciesLabels = ['canine' => 'Canino', 'feline' => 'Felino', 'avian' => 'Ave', 'exotic' => 'Exótico'];
                    @endphp
                    {{ $speciesLabels[$pet->species] ?? $pet->species }} - {{ $pet->breed ?? 'SRD' }}
                </p>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Gênero:</span>
                    <span>{{ $pet->gender === 'male' ? 'Macho' : 'Fêmea' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Idade:</span>
                    <span>{{ $pet->age ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Peso:</span>
                    <span>{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Porte:</span>
                    <span>{{ ucfirst($pet->size ?? '-') }}</span>
                </div>
                @if($pet->microchip)
                <div class="flex justify-between">
                    <span class="text-gray-500">Microchip:</span>
                    <span>{{ $pet->microchip }}</span>
                </div>
                @endif
                @if($pet->microchip_date)
                <div class="flex justify-between">
                    <span class="text-gray-500">Data Microchip:</span>
                    <span>{{ $pet->microchip_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($pet->rg_number)
                <div class="flex justify-between">
                    <span class="text-gray-500">RG Animal:</span>
                    <span>{{ $pet->rg_number }} @if($pet->rg_issuer)({{ $pet->rg_issuer }})@endif</span>
                </div>
                @endif
            </div>

            <div class="mt-6 space-y-2">
                <a href="{{ route('pets.edit', $pet) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
                <a href="{{ route('pets.timeline', $pet) }}" class="block w-full bg-teal-600 hover:bg-teal-700 text-white text-center py-2 rounded-lg">
                    <i class="fas fa-history mr-2"></i> Timeline
                </a>
            </div>
        </div>

        <!-- Tutores -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h4 class="font-semibold mb-4">Tutores</h4>
            @forelse($pet->tutors as $tutor)
            <a href="{{ route('tutors.show', $tutor) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                    {{ substr($tutor->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <div class="font-medium">{{ $tutor->name }}</div>
                    <div class="text-sm text-gray-500">{{ $tutor->pivot->is_primary ? 'Titular' : 'Secundário' }}</div>
                </div>
            </a>
            @empty
            <p class="text-gray-500 text-center py-2">Nenhum tutor vinculado.</p>
            @endforelse
        </div>
    </div>

    <!-- Histórico -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Últimos Atendimentos -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Últimos Atendimentos</h3>
                <a href="{{ route('medical-records.create') }}?pet_id={{ $pet->id }}" class="text-indigo-600 text-sm hover:underline">
                    <i class="fas fa-plus mr-1"></i> Novo
                </a>
            </div>

            @if($pet->medicalRecords->count() > 0)
            <div class="space-y-3">
                @foreach($pet->medicalRecords->take(5) as $record)
                <a href="{{ route('medical-records.show', $record) }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="font-medium">{{ $record->date->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($record->diagnosis, 50) ?: 'Sem diagnóstico' }}</div>
                    </div>
                    <div class="text-sm text-gray-500">{{ $record->vet->name ?? '-' }}</div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Nenhum atendimento registrado.</p>
            @endif
        </div>

        <!-- Vacinas -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Vacinas</h3>
                <a href="{{ route('vaccinations.create') }}?pet_id={{ $pet->id }}" class="text-indigo-600 text-sm hover:underline">
                    <i class="fas fa-plus mr-1"></i> Nova
                </a>
            </div>

            @if($pet->vaccinations->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vacina</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Próxima</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($pet->vaccinations as $vaccination)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $vaccination->vaccine }}</td>
                        <td class="px-4 py-2 text-sm">{{ $vaccination->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $vaccination->next_date ? $vaccination->next_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-center py-4">Nenhuma vacina registrada.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.adminlte', ['title' => $zoonoticDisease->name])

@section('header')
    <a href="{{ route('zoonotic-diseases.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $zoonoticDisease->name }}</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if($zoonoticDisease->is_notifiable)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <div>
                <strong class="text-red-800">Doença de Notificação Obrigatória</strong>
                <p class="text-red-600 text-sm">Esta doença deve ser notificada às autoridades sanitárias conforme legislação vigente.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Categoria</h4>
                <p><span class="px-2 py-1 rounded text-sm
                    @switch($zoonoticDisease->category)
                        @case('viral') bg-red-100 text-red-800 @break
                        @case('bacterial') bg-yellow-100 text-yellow-800 @break
                        @case('parasitic') bg-blue-100 text-blue-800 @break
                        @case('fungal') bg-gray-100 text-gray-800 @break
                        @default bg-dark-100 text-dark-800
                    @endswitch
                ">{{ $zoonoticDisease->category_label }}</span></p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Agente Causador</h4>
                <p class="font-semibold">{{ $zoonoticDisease->causative_agent ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Período de Incubação</h4>
                <p>{{ $zoonoticDisease->incubation_period ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Notificação Obrigatória</h4>
                <p>{{ $zoonoticDisease->is_notifiable ? 'Sim' : 'Não' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($zoonoticDisease->species_affected)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-paw mr-2"></i>Espécies Atingidas</h3>
            <div class="flex flex-wrap gap-2">
                @php
                    $speciesLabels = [
                        'canine' => 'Cães', 'feline' => 'Gatos', 'equine' => 'Equinos',
                        'bovine' => 'Bovinos', 'ovine' => 'Ovinos', 'caprine' => 'Caprinos',
                        'swine' => 'Suínos', 'avian' => 'Aves', 'reptile' => 'Répteis',
                        'rodents' => 'Roedores', 'wild_mammals' => 'Mamíferos Silvestres',
                        'wild_canids' => 'Canídeos Silvestres', 'wild_felids' => 'Felídeos Silvestres',
                        'non_human_primates' => 'Primatas Não Humanos',
                        'wild_birds' => 'Aves Silvestres', 'wild_ungulates' => 'Ungulados Silvestres',
                        'ferrets' => 'Furões', 'fish' => 'Peixes',
                        'asinine' => 'Asininos', 'mule' => 'Muares',
                        'human' => 'Humanos', 'psittacidae' => 'Psitacídeos',
                    ];
                @endphp
                @foreach($zoonoticDisease->species_affected as $species)
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                    {{ $speciesLabels[$species] ?? $species }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-arrow-right mr-2"></i>Transmissão</h3>
            <p class="text-gray-700">{{ $zoonoticDisease->transmission ?? 'Informação não disponível.' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-dog mr-2"></i>Sintomas em Animais</h3>
            <p class="text-gray-700">{{ $zoonoticDisease->animal_symptoms ?? 'Informação não disponível.' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-user mr-2"></i>Sintomas em Humanos</h3>
            <p class="text-gray-700">{{ $zoonoticDisease->human_symptoms ?? 'Informação não disponível.' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-shield-alt mr-2"></i>Prevenção</h3>
            <p class="text-gray-700">{{ $zoonoticDisease->prevention ?? 'Informação não disponível.' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-ambulance mr-2"></i>Tratamento</h3>
            <p class="text-gray-700">{{ $zoonoticDisease->treatment ?? 'Informação não disponível.' }}</p>
        </div>
    </div>

    @if($zoonoticDisease->notes)
    <div class="bg-yellow-50 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-2"><i class="fas fa-sticky-note mr-2"></i>Observações</h3>
        <p class="text-gray-700">{{ $zoonoticDisease->notes }}</p>
    </div>
    @endif

    @if($zoonoticDisease->medicalRecords->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-file-medical mr-2"></i>Registros em Prontuários ({{ $zoonoticDisease->medicalRecords->count() }})</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pet</th>
                    <th>Confirmado / Suspeito</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zoonoticDisease->medicalRecords as $record)
                <tr>
                    <td>{{ $record->date->format('d/m/Y') }}</td>
                    <td><a href="{{ route('medical-records.show', $record) }}">{{ $record->pet->name ?? '-' }}</a></td>
                    <td>
                        @if($record->pivot->is_suspected)
                            <span class="badge badge-warning">Suspeito</span>
                        @else
                            <span class="badge badge-danger">Confirmado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="flex justify-between">
        <a href="{{ route('zoonotic-diseases.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <a href="{{ route('zoonotic-diseases.edit', $zoonoticDisease) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit mr-2"></i> Editar
        </a>
    </div>
</div>
@endsection

@extends('layouts.app', ['title' => $tutor->name])

@section('header')
    <a href="{{ route('tutors.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">{{ $tutor->name }}</h2>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Dados do Tutor -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-2xl font-bold">
                    {{ substr($tutor->name, 0, 1) }}
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold">{{ $tutor->name }}</h3>
                    <p class="text-gray-500 text-sm">Tutor desde {{ $tutor->created_at->format('M/Y') }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-id-card w-6 text-gray-400"></i>
                    <span>{{ $tutor->cpf }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-6 text-gray-400"></i>
                    <span>{{ $tutor->phone }}</span>
                </div>
                @if($tutor->phone_secondary)
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-6 text-gray-400"></i>
                    <span>{{ $tutor->phone_secondary }}</span>
                </div>
                @endif
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-6 text-gray-400"></i>
                    <span>{{ $tutor->email }}</span>
                </div>
                @if($tutor->address)
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-map-marker-alt w-6 text-gray-400"></i>
                    <span>{{ $tutor->address }}, {{ $tutor->city ? $tutor->city . ' - ' . $tutor->state : '' }}</span>
                </div>
                @endif
            </div>

            <div class="mt-6 flex gap-2">
                <a href="{{ route('tutors.edit', $tutor) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Pets e Histórico -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Pets -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Pets</h3>
                <a href="{{ route('pets.create') }}?tutor_id={{ $tutor->id }}" class="text-indigo-600 text-sm hover:underline">
                    <i class="fas fa-plus mr-1"></i> Novo Pet
                </a>
            </div>

            @if($tutor->pets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($tutor->pets as $pet)
                <a href="{{ route('pets.show', $pet) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium">{{ $pet->name }}</div>
                        <div class="text-sm text-gray-500">{{ ucfirst($pet->species) }} - {{ $pet->breed ?? 'SRD' }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">Nenhum pet cadastrado.</p>
            @endif
        </div>

        <!-- Faturas Recentes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Faturas</h3>
                <a href="{{ route('invoices.create') }}?tutor_id={{ $tutor->id }}" class="text-indigo-600 text-sm hover:underline">
                    <i class="fas fa-plus mr-1"></i> Nova Fatura
                </a>
            </div>

            @if($tutor->invoices->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($tutor->invoices->take(5) as $invoice)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2 text-sm">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            @php
                                $statusClass = match($invoice->status) {
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-center py-4">Nenhuma fatura registrada.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app', ['title' => 'Relatório Financeiro'])

@section('header')
    <h2 class="text-lg font-semibold">Relatório Financeiro</h2>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" class="flex gap-4 items-end flex-wrap">
            <div>
                <label class="block text-xs text-gray-500 uppercase">Data Início</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase">Data Fim</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="px-4 py-2 border rounded-lg">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Receita Total</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">A Receber</p>
                    <p class="text-2xl font-bold text-yellow-600">R$ {{ number_format($totalPending, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Vencidas</p>
                    <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalOverdue, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Descontos</p>
                    <p class="text-2xl font-bold text-gray-600">R$ {{ number_format($totalDiscounts, 2, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-percent text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Receita por Forma de Pagamento</h3>
            <div class="space-y-3">
                @forelse($revenueByPaymentMethod as $method)
                @php
                    $methodLabels = [
                        'pix' => 'PIX',
                        'dinheiro' => 'Dinheiro',
                        'cartao_credito' => 'Cartão de Crédito',
                        'cartao_debito' => 'Cartão de Débito',
                        'boleto' => 'Boleto',
                        'transferencia' => 'Transferência',
                        'convenio' => 'Convênio'
                    ];
                    $label = $methodLabels[$method->payment_method] ?? $method->payment_method;
                @endphp
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span>{{ $label }}</span>
                    <div class="text-right">
                        <span class="font-semibold">R$ {{ number_format($method->total, 2, ',', '.') }}</span>
                        <span class="text-xs text-gray-500 ml-2">({{ $method->count }})</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum pagamento registrado</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Atendimentos por Status</h3>
            <div class="space-y-3">
                @php
                    $statusLabels = [
                        'scheduled' => 'Agendados',
                        'confirmed' => 'Confirmados',
                        'in_progress' => 'Em Andamento',
                        'completed' => 'Concluídos',
                        'cancelled' => 'Cancelados',
                        'no_show' => 'Faltantes'
                    ];
                    $statusColors = [
                        'scheduled' => 'bg-blue-100 text-blue-800',
                        'confirmed' => 'bg-indigo-100 text-indigo-800',
                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        'no_show' => 'bg-gray-100 text-gray-800'
                    ];
                @endphp
                @forelse($appointmentStats as $status => $stat)
                <div class="flex justify-between items-center p-3 rounded-lg">
                    <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$status] ?? 'bg-gray-100' }}">
                        {{ $statusLabels[$status] ?? $status }}
                    </span>
                    <span class="font-semibold">{{ $stat->count }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum atendimento registrado</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Principais Clientes</h3>
            <div class="space-y-3">
                @forelse($topClients as $index => $client)
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600 mr-2">{{ $index + 1 }}</span>
                        <span class="truncate">{{ $client->name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold text-sm">R$ {{ number_format($client->total, 2, ',', '.') }}</span>
                        <span class="text-xs text-gray-500 ml-1">({{ $client->count }})</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum cliente registrado</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Serviços Mais Rentáveis</h3>
            <div class="space-y-3">
                @forelse($serviceStats as $service)
                <div class="flex justify-between items-center">
                    <span class="truncate">{{ $service->name }}</span>
                    <div class="text-right">
                        <span class="font-semibold text-sm">R$ {{ number_format($service->revenue, 2, ',', '.') }}</span>
                        <span class="text-xs text-gray-500 ml-1">({{ $service->count }})</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhum serviço registrado</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Estatísticas Gerais</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                    <span class="text-sm">Vacinas Aplicadas</span>
                    <span class="font-bold text-green-600">{{ $vaccinationStats }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm">Exames Realizados</span>
                    <span class="font-bold text-blue-600">{{ $examStats }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                    <span class="text-sm">Cirurgias</span>
                    <span class="font-bold text-purple-600">{{ $surgeryStats }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                    <span class="text-sm">Total de Pets</span>
                    <span class="font-bold text-indigo-600">{{ $totalPets }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-teal-50 rounded-lg">
                    <span class="text-sm">Pets Ativos</span>
                    <span class="font-bold text-teal-600">{{ $activePets }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-pink-50 rounded-lg">
                    <span class="text-sm">Novos Pets (período)</span>
                    <span class="font-bold text-pink-600">{{ $newPets }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-red-800 mb-4"><i class="fas fa-exclamation-triangle mr-2"></i>Produtos com Estoque Baixo</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($lowStockProducts as $product)
            <div class="bg-white p-4 rounded-lg">
                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-sm text-red-600">Estoque: {{ $product->stock }} / Mínimo: {{ $product->min_stock }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($overdueInvoices->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-yellow-800 mb-4"><i class="fas fa-clock mr-2"></i>Faturas Vencidas</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase">
                        <th class="pb-2">Fatura</th>
                        <th class="pb-2">Cliente</th>
                        <th class="pb-2">Valor</th>
                        <th class="pb-2">Vencimento</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($overdueInvoices as $invoice)
                    <tr>
                        <td class="py-2">{{ $invoice->invoice_number }}</td>
                        <td class="py-2">{{ $invoice->tutor->name ?? '-' }}</td>
                        <td class="py-2">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                        <td class="py-2">{{ $invoice->due_date->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">Distribuição de Pets por Espécie</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $speciesLabels = [
                    'canine' => 'Caninos',
                    'feline' => 'Felinos',
                    'avian' => 'Aves',
                    'exotic' => 'Exóticos',
                    'reptile' => 'Répteis',
                    'small_mammal' => 'Pequenos Mamíferos'
                ];
                $speciesColors = [
                    'canine' => 'bg-amber-500',
                    'feline' => 'bg-blue-500',
                    'avian' => 'bg-green-500',
                    'exotic' => 'bg-purple-500',
                    'reptile' => 'bg-teal-500',
                    'small_mammal' => 'bg-pink-500'
                ];
            @endphp
            @forelse($speciesBreakdown as $species => $stat)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="w-16 h-16 {{ $speciesColors[$species] ?? 'bg-gray-500' }} rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-white font-bold text-xl">{{ $stat->count }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ $speciesLabels[$species] ?? $species }}</p>
            </div>
            @empty
            <p class="col-span-4 text-gray-500 text-center py-4">Nenhum pet registrado</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

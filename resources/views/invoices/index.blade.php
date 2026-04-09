@extends('layouts.adminlte', ['title' => 'Faturas'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Faturas</h2>
        <a href="{{ route('invoices.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Nova Fatura
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Todos Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pago</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Vencido</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $inv->invoice_number }}</td>
                    <td class="px-6 py-4">{{ $inv->tutor->name ?? '-' }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($inv->total, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @php $statusClass = match($inv->status) { 'paid' => 'bg-green-100 text-green-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'overdue' => 'bg-red-100 text-red-800', 'cancelled' => 'bg-gray-100 text-gray-800', default => 'bg-gray-100' }; @endphp
                        @php $statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'overdue' => 'Vencido', 'cancelled' => 'Cancelado', 'refunded' => 'Estornado']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $statusLabels[$inv->status] ?? $inv->status }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('invoices.edit', $inv) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhuma fatura encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $invoices->links() }}</div>
</div>
@endsection

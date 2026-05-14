@extends('portal.layouts.app', ['title' => 'Faturas'])

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Faturas</h1>
</div>

@if($invoices->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
    <i class="fas fa-file-invoice text-gray-300 text-5xl mb-4"></i>
    <p class="text-gray-500">Nenhuma fatura encontrada.</p>
</div>
@else
<div class="space-y-3">
    @foreach($invoices as $invoice)
    <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 flex items-center justify-between hover:shadow-md transition">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center
                {{ $invoice->status === 'paid' ? 'bg-green-100' : ($invoice->status === 'overdue' ? 'bg-red-100' : 'bg-yellow-100') }}">
                <i class="fas fa-file-invoice text-lg
                    {{ $invoice->status === 'paid' ? 'text-green-600' : ($invoice->status === 'overdue' ? 'text-red-600' : 'text-yellow-600') }}"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">Fatura #{{ $invoice->id }}</p>
                <p class="text-xs text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-800">R$ {{ number_format($invoice->amount ?? 0, 2, ',', '.') }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full
                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                {{ $invoice->status === 'paid' ? 'Pago' : ($invoice->status === 'pending' ? 'Pendente' : ($invoice->status === 'overdue' ? 'Vencido' : 'Cancelado')) }}
            </span>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection

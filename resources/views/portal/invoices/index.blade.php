@extends('portal.layouts.app', ['title' => 'Faturas'])

@section('content')
<div class="mb-4">
    <a href="{{ route('portal.dashboard') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Início
    </a>
</div>

<div class="flex items-center justify-between mb-6">
    <h1 class="portal-section-title text-2xl sm:text-3xl mb-0">
        <i class="fas fa-file-invoice"></i>
        Faturas
    </h1>
</div>

@if($invoices->isEmpty())
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-file-invoice"></i>
    <p>Nenhuma fatura encontrada.</p>
</div>
@else
<div class="space-y-4">
    @foreach($invoices as $invoice)
    <a href="{{ route('portal.invoices.show', $invoice->id) }}" class="portal-card p-5 flex items-center justify-between hover:shadow-lg transition portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="portal-icon-wrapper w-14 h-14
                {{ $invoice->status === 'paid' ? 'bg-green-100' : ($invoice->status === 'overdue' ? 'bg-red-100' : 'bg-yellow-100') }}">
                <i class="fas fa-file-invoice text-2xl
                    {{ $invoice->status === 'paid' ? 'text-green-600' : ($invoice->status === 'overdue' ? 'text-red-600' : 'text-yellow-600') }}"></i>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800">Fatura #{{ $invoice->id }}</p>
                <p class="text-base text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xl font-bold text-gray-800">R$ {{ number_format($invoice->amount ?? 0, 2, ',', '.') }}</p>
            <span class="portal-badge
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

@extends('portal.layouts.app', ['title' => 'Fatura #' . $invoice->id])

@section('content')
<div class="mb-6">
    <a href="{{ route('portal.invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-1"></i>Faturas
    </a>
</div>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Fatura #{{ $invoice->id }}</h1>
            <a href="{{ route('portal.invoices.download', $invoice->id) }}"
               class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
        <span class="text-sm px-3 py-1 rounded-full
            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
            {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
            {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
            {{ $invoice->status === 'paid' ? 'Pago' : ($invoice->status === 'pending' ? 'Pendente' : ($invoice->status === 'overdue' ? 'Vencido' : 'Cancelado')) }}
        </span>
    </div>

    <div class="space-y-3 text-sm mb-8">
        <div class="flex justify-between py-2 border-b border-gray-100">
            <span class="text-gray-500">Data de emissão</span>
            <span class="font-medium text-gray-800">{{ $invoice->created_at->format('d/m/Y') }}</span>
        </div>
        @if($invoice->due_date)
        <div class="flex justify-between py-2 border-b border-gray-100">
            <span class="text-gray-500">Vencimento</span>
            <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($invoice->pet)
        <div class="flex justify-between py-2 border-b border-gray-100">
            <span class="text-gray-500">Pet</span>
            <span class="font-medium text-gray-800">{{ $invoice->pet->name }}</span>
        </div>
        @endif
        <div class="flex justify-between py-2 text-lg font-bold">
            <span class="text-gray-700">Valor total</span>
            <span class="text-gray-800">R$ {{ number_format($invoice->amount ?? 0, 2, ',', '.') }}</span>
        </div>
    </div>

    @if(in_array($invoice->status, ['pending', 'overdue']) && $invoice->pix_code)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
        <h3 class="text-sm font-semibold text-blue-800 mb-3">Pagamento via PIX</h3>
        <div class="mb-3">
            {!! $invoice->pix_code !!}
        </div>
        <button onclick="copyPix()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fas fa-copy"></i>Copiar código PIX
        </button>
        <p class="text-xs text-blue-600 mt-2">Escaneie o QR Code ou copie o código para pagar</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function copyPix() {
    const pixDiv = document.querySelector('.bg-blue-50');
    const img = pixDiv.querySelector('img');
    if (img && img.alt) {
        navigator.clipboard.writeText(img.alt).then(() => {
            alert('Código PIX copiado!');
        });
    }
}
</script>
@endpush

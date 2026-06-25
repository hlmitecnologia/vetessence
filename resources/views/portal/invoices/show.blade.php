@extends('portal.layouts.app', ['title' => 'Fatura #' . $invoice->id])

@section('content')
<div class="mb-6">
    <a href="{{ route('portal.invoices.index') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Faturas
    </a>
</div>

<div class="max-w-2xl mx-auto portal-card p-8 sm:p-10 portal-fade-in">
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Fatura #{{ $invoice->id }}</h1>
            <span class="portal-badge mt-2
                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                {{ $invoice->status === 'paid' ? 'Pago' : ($invoice->status === 'pending' ? 'Pendente' : ($invoice->status === 'overdue' ? 'Vencido' : 'Cancelado')) }}
            </span>
        </div>
        <a href="{{ route('portal.invoices.download', $invoice->id) }}"
           class="portal-btn bg-blue-50 hover:bg-blue-100 text-blue-700 text-base">
            <i class="fas fa-download"></i>
            Download PDF
        </a>
    </div>

    <div class="space-y-4 text-base mb-8">
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Data de emissão</span>
            <span class="font-semibold text-gray-800">{{ $invoice->created_at->format('d/m/Y') }}</span>
        </div>
        @if($invoice->due_date)
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Vencimento</span>
            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($invoice->pet)
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Pet</span>
            <span class="font-semibold text-gray-800">{{ $invoice->pet->name }}</span>
        </div>
        @endif
        <div class="flex justify-between py-3 text-2xl font-bold">
            <span class="text-gray-700">Valor total</span>
            <span class="text-gray-800">R$ {{ number_format($invoice->amount ?? 0, 2, ',', '.') }}</span>
        </div>
    </div>

    @if(in_array($invoice->status, ['pending', 'overdue']) && $invoice->pix_code)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
        <h3 class="text-lg font-bold text-blue-800 mb-4">Pagamento via PIX</h3>
        <div class="mb-4">
            {!! $invoice->pix_code !!}
        </div>
        <button onclick="copyPix()"
            class="portal-btn bg-blue-600 hover:bg-blue-700 text-white font-semibold">
            <i class="fas fa-copy"></i>Copiar código PIX
        </button>
        <p class="text-base text-blue-600 mt-3">Escaneie o QR Code ou copie o código para pagar</p>
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

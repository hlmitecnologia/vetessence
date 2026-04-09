@extends('layouts.adminlte', ['title' => 'Fatura'])

@section('header')
    <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Fatura {{ $invoice->invoice_number }}</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $invoice->invoice_number }}</h2>
                        <p class="text-gray-500">Emitida em {{ $invoice->created_at->format('d/m/Y') }}</p>
                    </div>
                    @php $statusClass = match($invoice->status) { 'paid' => 'bg-green-100 text-green-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'overdue' => 'bg-red-100 text-red-800', default => 'bg-gray-100' }; @endphp
                    <span class="px-4 py-2 text-sm rounded-full {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div><h4 class="text-xs text-gray-500 uppercase">Tutor</h4><p class="font-semibold">{{ $invoice->tutor->name ?? '-' }}</p></div>
                    <div><h4 class="text-xs text-gray-500 uppercase">Vencimento</h4><p>{{ $invoice->due_date->format('d/m/Y') }}</p></div>
                </div>

                <table class="w-full mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs">Descrição</th>
                            <th class="px-4 py-2 text-right text-xs">Qtd</th>
                            <th class="px-4 py-2 text-right text-xs">Valor Unit.</th>
                            <th class="px-4 py-2 text-right text-xs">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->description }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2"><span>Subtotal:</span><span>R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</span></div>
                        @if($invoice->discount > 0)
                        <div class="flex justify-between py-2 text-red-600"><span>Desconto:</span><span>- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span></div>
                        @endif
                        <div class="flex justify-between py-2 font-bold text-lg border-t pt-2"><span>Total:</span><span>R$ {{ number_format($invoice->total, 2, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            @if($invoice->status === 'pending' || $invoice->status === 'overdue')
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6" id="pix-section">
                <h4 class="font-semibold mb-4 text-center"><i class="fab fa-whatsapp text-green-600 mr-2"></i>Pagamento via PIX</h4>
                
                <div id="pix-loading" class="text-center py-4">
                    <button onclick="generatePix()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg w-full">
                        <i class="fas fa-qrcode mr-2"></i> Gerar QR Code PIX
                    </button>
                </div>

                <div id="pix-qrcode" class="hidden text-center">
                    <img id="qrcode-image" src="" alt="QR Code PIX" class="mx-auto mb-4 w-48 h-48">
                    <p class="text-sm text-gray-600 mb-2">Valor: <strong>R$ {{ number_format($invoice->total, 2, ',', '.') }}</strong></p>
                    <p class="text-xs text-gray-500 mb-4" id="pix-expiration"></p>
                    <div class="bg-gray-100 p-2 rounded text-xs break-all" id="pix-payload"></div>
                    <button onclick="copyPix()" class="mt-3 text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-copy mr-1"></i> Copiar código PIX
                    </button>
                </div>
            </div>
            @endif

            @if($invoice->status === 'pending' || $invoice->status === 'overdue')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold mb-3">Registrar Pagamento</h4>
                <form action="{{ route('invoices.pay', $invoice) }}" method="POST">
                    @csrf
                    <select name="payment_method" required class="w-full px-4 py-2 border rounded-lg mb-3">
                        <option value="">Selecione...</option>
                        <option value="pix">PIX</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="cartao_credito">Cartão de Crédito</option>
                        <option value="cartao_debito">Cartão de Débito</option>
                        <option value="transferencia">Transferência</option>
                    </select>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i> Confirmar
                    </button>
                </form>
            </div>
            @endif

            @if($invoice->status === 'paid')
            <div class="bg-green-50 rounded-xl shadow-sm p-6 text-center">
                <i class="fas fa-check-circle text-green-600 text-4xl mb-3"></i>
                <h4 class="font-semibold text-green-800">Pagamento Confirmado</h4>
                <p class="text-sm text-green-600 mt-2">{{ $invoice->paid_at->format('d/m/Y H:i') }}</p>
                <p class="text-sm">Método: {{ str_replace('_', ' ', ucfirst($invoice->payment_method)) }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        @if($invoice->status !== 'paid')
        <a href="{{ route('invoices.edit', $invoice) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPayload = '';

function generatePix() {
    const loading = document.getElementById('pix-loading');
    const qrcode = document.getElementById('pix-qrcode');
    
    loading.innerHTML = '<i class="fas fa-spinner fa-spin text-2xl text-green-600"></i>';
    
    fetch('{{ route('invoices.pix', $invoice) }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('qrcode-image').src = data.qrcode;
            document.getElementById('pix-payload').textContent = data.payload;
            document.getElementById('pix-expiration').textContent = 'Expira em: ' + new Date(data.expiration).toLocaleString('pt-BR');
            currentPayload = data.payload;
            
            loading.classList.add('hidden');
            qrcode.classList.remove('hidden');
        })
        .catch(error => {
            loading.innerHTML = '<p class="text-red-600">Erro ao gerar QR Code</p>';
            console.error(error);
        });
}

function copyPix() {
    navigator.clipboard.writeText(currentPayload).then(() => {
        alert('Código PIX copiado!');
    });
}
</script>
@endpush

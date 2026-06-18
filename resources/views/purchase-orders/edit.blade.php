@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-shopping-cart"></i> Editar Pedido {{ $purchaseOrder->order_number }}</h4>
    <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Fornecedor</label>
                    <x-tom-select name="supplier_id" :value="old('supplier_id', $purchaseOrder->supplier_id)" required>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $purchaseOrder->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
                <div class="form-group">
                    <label>Unidade</label>
                    <x-tom-select name="branch_id" :value="old('branch_id', $purchaseOrder->branch_id)">
                        <option value="">Todas as unidades</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $purchaseOrder->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="2">{!! $purchaseOrder->notes !!}</textarea>
                    @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <h5>Itens</h5>
                <table class="table table-bordered" id="items-table">
                    <thead><tr><th>Produto</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody id="items-body">
                        @foreach($purchaseOrder->items as $idx => $item)
                            <tr>
                                <td>
                                    <select name="items[{{ $idx }}][product_id]" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="items[{{ $idx }}][quantity]" class="form-control qty" step="0.01" min="0.01" value="{{ $item->quantity }}"></td>
                                <td><input type="number" name="items[{{ $idx }}][unit_price]" class="form-control price" step="0.01" min="0" value="{{ $item->unit_price }}"></td>
                                <td class="subtotal">R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success" id="add-item"><i class="fas fa-plus"></i> Adicionar Item</button>
                <div class="text-right mt-2"><strong>Total: <span id="total-display">R$ {{ number_format($purchaseOrder->items->sum(fn($i) => $i->quantity * $i->unit_price), 2, ',', '.') }}</span></strong></div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('js')
<script>
let itemIndex = {{ $purchaseOrder->items->count() }};
document.getElementById('add-item')?.addEventListener('click', function() {
    let tbody = document.getElementById('items-body');
    let clone = tbody.children[0].cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => {
        let name = el.getAttribute('name').replace(/\[\d+\]/, `[${itemIndex}]`);
        el.setAttribute('name', name);
        el.value = '';
        el.addEventListener('input', calcSubtotal);
    });
    clone.querySelector('.subtotal').textContent = 'R$ 0,00';
    tbody.appendChild(clone);
    itemIndex++;
});
document.querySelectorAll('.qty, .price').forEach(el => el.addEventListener('input', calcSubtotal));
document.getElementById('items-body')?.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item') && document.querySelectorAll('#items-body tr').length > 1) {
        e.target.closest('tr').remove();
        calcTotal();
    }
});
function calcSubtotal() {
    let row = this.closest('tr');
    let qty = parseFloat(row.querySelector('.qty').value) || 0;
    let price = parseFloat(row.querySelector('.price').value) || 0;
    row.querySelector('.subtotal').textContent = 'R$ ' + (qty * price).toFixed(2).replace('.', ',');
    calcTotal();
}
function calcTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(el => {
        let val = el.textContent.replace('R$ ', '').replace('.', '').replace(',', '.');
        total += parseFloat(val) || 0;
    });
    document.getElementById('total-display').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}
</script>
@endpush

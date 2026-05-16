@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-shopping-cart"></i> Novo Pedido de Compra</h4>
    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Fornecedor</label>
                    <select name="supplier_id" class="form-control" required>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <h5>Itens</h5>
                <table class="table table-bordered" id="items-table">
                    <thead><tr><th>Produto</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody id="items-body">
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-control product-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->sale_price ?? 0 }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty" step="0.01" min="0.01" required></td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control price" step="0.01" min="0" required></td>
                            <td class="subtotal">R$ 0,00</td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success" id="add-item"><i class="fas fa-plus"></i> Adicionar Item</button>
                <div class="text-right mt-2"><strong>Total: <span id="total-display">R$ 0,00</span></strong></div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Salvar Rascunho</button>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('js')
<script>
let itemIndex = 1;
document.getElementById('add-item').addEventListener('click', function() {
    let tbody = document.getElementById('items-body');
    let clone = tbody.children[0].cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => {
        let name = el.getAttribute('name').replace(/\[\d+\]/, `[${itemIndex}]`);
        el.setAttribute('name', name);
        el.value = '';
        el.removeEventListener('input', calcSubtotal);
        el.removeEventListener('change', calcSubtotal);
        if (el.classList.contains('qty') || el.classList.contains('price')) {
            el.addEventListener('input', calcSubtotal);
        }
        if (el.tagName === 'SELECT') {
            el.addEventListener('change', setDefaultPrice);
        }
    });
    clone.querySelector('.subtotal').textContent = 'R$ 0,00';
    tbody.appendChild(clone);
    itemIndex++;
});
document.querySelectorAll('.qty, .price').forEach(el => el.addEventListener('input', calcSubtotal));
document.querySelectorAll('.product-select').forEach(el => el.addEventListener('change', setDefaultPrice));
document.getElementById('items-body').addEventListener('click', function(e) {
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
function setDefaultPrice() {
    let row = this.closest('tr');
    let opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.price) {
        row.querySelector('.price').value = opt.dataset.price;
        calcSubtotal.call(row.querySelector('.price'));
    }
}
</script>
@endpush

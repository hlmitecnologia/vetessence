@extends('layouts.adminlte', ['title' => 'Nova Fatura'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tutor *</label>
                                <x-tom-select name="tutor_id" :value="old('tutor_id', $tutor->id ?? '')" required>
                                    <option value="">Selecione...</option>
                                    @foreach($tutors as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </x-tom-select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vencimento *</label>
                                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required class="form-control">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4"><i class="fas fa-list mr-2"></i>Itens</h5>
                    <hr>

                    <ul class="nav nav-tabs" id="itemTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="services-tab" data-toggle="tab" href="#services" role="tab">
                                <i class="fas fa-concierge-bell mr-1"></i>Serviços
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" role="tab">
                                <i class="fas fa-box mr-1"></i>Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="avulso-tab" data-toggle="tab" href="#avulso" role="tab">
                                <i class="fas fa-pencil-alt mr-1"></i>Avulso
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-3 bg-light border border-top-0 rounded-bottom mb-3">
                        <div class="tab-pane fade show active" id="services" role="tabpanel">
                            <div class="row align-items-end">
                                <div class="col-md-7">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Serviço</label>
                                        <select id="service-select" class="form-control form-control-sm select2">
                                            <option value="">Selecione um serviço...</option>
                                            @foreach($services as $s)
                                                <option value="{{ $s->id }}" data-name="{{ $s->name }}" data-price="{{ $s->price }}">{{ $s->name }} — R$ {{ number_format($s->price, 2, ',', '.') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Qtd</label>
                                        <input type="number" id="service-qty" value="1" min="1" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="addServiceItem()" class="btn btn-sm btn-primary btn-block mt-1">
                                        <i class="fas fa-plus mr-1"></i>Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="products" role="tabpanel">
                            <div class="row align-items-end">
                                <div class="col-md-7">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Produto</label>
                                        <select id="product-select" class="form-control form-control-sm select2">
                                            <option value="">Selecione um produto...</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->sale_price }}" data-stock="{{ $p->stock }}">
                                                    {{ $p->name }} — R$ {{ number_format($p->sale_price, 2, ',', '.') }} ({{ $p->stock }} unid.)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Qtd</label>
                                        <input type="number" id="product-qty" value="1" min="1" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="addProductItem()" class="btn btn-sm btn-primary btn-block mt-1">
                                        <i class="fas fa-plus mr-1"></i>Adicionar
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">Apenas produtos com estoque &gt; 0 são exibidos.</small>
                        </div>

                        <div class="tab-pane fade" id="avulso" role="tabpanel">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Descrição</label>
                                        <input type="text" id="avulso-desc" class="form-control form-control-sm" placeholder="Descrição do item avulso">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Qtd</label>
                                        <input type="number" id="avulso-qty" value="1" min="1" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted">Valor Unit.</label>
                                        <input type="number" id="avulso-price" step="0.01" min="0" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="addAvulsoItem()" class="btn btn-sm btn-primary btn-block mt-1">
                                        <i class="fas fa-plus mr-1"></i>Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:60px">Tipo</th>
                                    <th>Descrição</th>
                                    <th style="width:80px">Qtd</th>
                                    <th style="width:120px">Valor Unit.</th>
                                    <th style="width:120px">Total</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Subtotal:</td>
                                    <td id="subtotal-display">R$ 0,00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <p class="text-muted small mb-0">
                            <span class="badge badge-primary">S</span> Serviço (emite NFSe)
                            <span class="badge badge-success ml-2">P</span> Produto (emite NF-e + baixa estoque)
                            <span class="badge badge-secondary ml-2">A</span> Avulso (sem nota fiscal)
                        </p>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Criar Fatura</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let items = [];
let itemIndex = 0;

function formatMoney(value) {
    return 'R$ ' + value.toFixed(2).replace('.', ',');
}

function renderItems() {
    const tbody = document.getElementById('items-tbody');
    let subtotal = 0;
    let html = '';

    items.forEach((item, idx) => {
        const total = item.quantity * item.unit_price;
        subtotal += total;

        let badge = '';
        if (item.item_type === 'service') badge = '<span class="badge badge-primary">S</span>';
        else if (item.item_type === 'product') badge = '<span class="badge badge-success">P</span>';
        else badge = '<span class="badge badge-secondary">A</span>';

        html += `<tr>
            <td class="text-center">${badge}</td>
            <td>${escapeHtml(item.description)}</td>
            <td class="text-right">${item.quantity}</td>
            <td class="text-right">${formatMoney(item.unit_price)}</td>
            <td class="text-right">${formatMoney(total)}</td>
            <td class="text-center">
                <button type="button" onclick="removeItem(${idx})" class="btn btn-sm btn-outline-danger py-0 px-1">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html || `<tr><td colspan="6" class="text-center text-muted py-3">Nenhum item adicionado. Use as abas acima para adicionar serviços, produtos ou itens avulsos.</td></tr>`;
    document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function addServiceItem() {
    const select = document.getElementById('service-select');
    const option = select.options[select.selectedIndex];
    if (!option || !option.value) {
        alert('Selecione um serviço.');
        return;
    }
    const qty = parseInt(document.getElementById('service-qty').value) || 1;
    items.push({
        description: option.dataset.name,
        quantity: qty,
        unit_price: parseFloat(option.dataset.price),
        item_type: 'service',
        product_id: null,
        service_id: parseInt(option.value),
    });
    select.value = '';
    renderItems();
}

function addProductItem() {
    const select = document.getElementById('product-select');
    const option = select.options[select.selectedIndex];
    if (!option || !option.value) {
        alert('Selecione um produto.');
        return;
    }
    const qty = parseInt(document.getElementById('product-qty').value) || 1;
    const stock = parseInt(option.dataset.stock);
    if (qty > stock) {
        alert(`Estoque insuficiente. Disponível: ${stock} unid.`);
        return;
    }
    items.push({
        description: option.dataset.name,
        quantity: qty,
        unit_price: parseFloat(option.dataset.price),
        item_type: 'product',
        product_id: parseInt(option.value),
        service_id: null,
    });
    select.value = '';
    document.getElementById('product-qty').value = 1;
    renderItems();
}

function addAvulsoItem() {
    const desc = document.getElementById('avulso-desc').value.trim();
    if (!desc) {
        alert('Preencha a descrição do item avulso.');
        return;
    }
    const qty = parseInt(document.getElementById('avulso-qty').value) || 1;
    const price = parseFloat(document.getElementById('avulso-price').value);
    if (!price || price <= 0) {
        alert('Informe um valor unitário válido.');
        return;
    }
    items.push({
        description: desc,
        quantity: qty,
        unit_price: price,
        item_type: 'avulso',
        product_id: null,
        service_id: null,
    });
    document.getElementById('avulso-desc').value = '';
    document.getElementById('avulso-qty').value = 1;
    document.getElementById('avulso-price').value = '';
    renderItems();
}

function removeItem(idx) {
    items.splice(idx, 1);
    renderItems();
}

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    if (items.length === 0) {
        e.preventDefault();
        alert('Adicione pelo menos um item à fatura.');
        return;
    }

    items.forEach((item, idx) => {
        const prefix = `items[${idx}]`;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = prefix + '[description]';
        input.value = item.description;
        this.appendChild(input);

        const qty = document.createElement('input');
        qty.type = 'hidden';
        qty.name = prefix + '[quantity]';
        qty.value = item.quantity;
        this.appendChild(qty);

        const price = document.createElement('input');
        price.type = 'hidden';
        price.name = prefix + '[unit_price]';
        price.value = item.unit_price;
        this.appendChild(price);

        const type = document.createElement('input');
        type.type = 'hidden';
        type.name = prefix + '[item_type]';
        type.value = item.item_type;
        this.appendChild(type);

        if (item.product_id) {
            const pid = document.createElement('input');
            pid.type = 'hidden';
            pid.name = prefix + '[product_id]';
            pid.value = item.product_id;
            this.appendChild(pid);
        }

        if (item.service_id) {
            const sid = document.createElement('input');
            sid.type = 'hidden';
            sid.name = prefix + '[service_id]';
            sid.value = item.service_id;
            this.appendChild(sid);
        }
    });
});

$(function() {
    $('.select2').select2({
        width: '100%',
        theme: 'bootstrap4',
    });
});
</script>
@endpush
@endsection
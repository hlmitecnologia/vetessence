@extends('layouts.adminlte', ['title' => 'Nova Solicitação'])
@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('convenio-claims.store') }}">@csrf
            <div class="form-group"><label>Pet / Convênio</label><select name="convenio_pet_id" class="form-control" required>
                <option value="">Selecione</option>
                @foreach($convenioPets as $cp)<option value="{{ $cp->id }}">{{ optional($cp->pet)->name }} - {{ optional($cp->convenio)->name }}</option>@endforeach
            </select></div>
            <div class="form-group"><label>Fatura</label><select name="invoice_id" class="form-control">
                <option value="">Nenhuma</option>
                @foreach($invoices as $inv)<option value="{{ $inv->id }}">#{{ $inv->invoice_number }} - R$ {{ number_format($inv->total, 2, ',', '.') }}</option>@endforeach
            </select></div>
            <div class="form-group"><label>Valor Solicitado</label><input name="amount_requested" type="number" step="0.01" class="form-control" required></div>
            <div class="form-group"><label>Observações</label><textarea name="notes" class="form-control"></textarea></div>
            <button type="submit" class="btn btn-primary">Criar</button>
        </form>
    </div></div>
@endsection

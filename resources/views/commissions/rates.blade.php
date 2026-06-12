@php $title = 'Taxas de Comissão'; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Nova Taxa</h3></div>
            <div class="card-body">
                <form action="{{ route('commissions.rates-store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Veterinário *</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            @foreach($vets as $vet)
                            <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo *</label>
                        <select name="commissionable_type" class="form-control" required>
                            <option value="service">Serviço</option>
                            <option value="product">Produto</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Item *</label>
                        <select name="commissionable_id" class="form-control" required>
                            <optgroup label="Serviços">
                                @foreach($services as $svc)
                                <option value="{{ $svc->id }}" data-type="service">{{ $svc->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Produtos">
                                @foreach($products as $prod)
                                <option value="{{ $prod->id }}" data-type="product">{{ $prod->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Taxa *</label>
                        <select name="rate_type" class="form-control" required>
                            <option value="percentage">Percentual (%)</option>
                            <option value="fixed">Valor Fixo (R$)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valor *</label>
                        <input type="number" name="rate_value" class="form-control" step="0.01" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Taxas Cadastradas</h3></div>
            <div class="card-body">
                @if($rates->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Veterinário</th>
                            <th>Item</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Ativo</th>
                            <th style="width: 80px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $rate)
                        <tr>
                            <td>{{ $rate->user->name ?? '-' }}</td>
                            <td>
                                @if($rate->commissionable)
                                    {{ $rate->commissionable->name ?? '-' }}
                                    <small class="text-muted">({{ class_basename($rate->commissionable_type) }})</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $rate->rate_type === 'percentage' ? 'Percentual' : 'Fixo' }}</td>
                            <td>{{ $rate->rate_type === 'percentage' ? $rate->rate_value . '%' : 'R$ ' . number_format($rate->rate_value, 2, ',', '.') }}</td>
                            <td>
                                @if($rate->is_active)
                                    <span class="badge badge-success">Sim</span>
                                @else
                                    <span class="badge badge-danger">Não</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('commissions.rates-destroy', $rate) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="Excluir taxa?"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-center text-muted">Nenhuma taxa cadastrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

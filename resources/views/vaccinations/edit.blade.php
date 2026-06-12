@extends('layouts.adminlte', ['title' => 'Editar Vacina'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('vaccinations.update', $vaccination) }}" method="POST">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                    <div class="form-group">
                        <label>Vacina</label>
                        <input type="text" name="vaccine" value="{{ $vaccination->vaccine }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Produto (para baixa de estoque)</label>
                        <select name="product_id" class="form-control">
                            <option value="">— Sem produto —</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $vaccination->product_id == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} (estoque: {{ $p->stock }}) — R$ {{ number_format($p->sale_price, 2, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data</label>
                                <input type="date" name="date" value="{{ $vaccination->date->format('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próxima Dose</label>
                                <input type="date" name="next_date" value="{{ $vaccination->next_date ? $vaccination->next_date->format('Y-m-d') : '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Veterinário</label>
                                <select name="vet_id" class="form-control">
                                    @foreach($veterinarians as $vet)
                                    <option value="{{ $vet->id }}" {{ $vaccination->vet_id == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" rows="2" class="wysiwyg form-control">{!! $vaccination->notes !!}</textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
